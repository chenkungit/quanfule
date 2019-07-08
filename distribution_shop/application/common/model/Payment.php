<?php

namespace app\common\model;

use app\common\Enums\AccountLogEnums;
use app\common\Enums\RedisKeyEnums;
use app\service\Leader\LeaderStatusService;
use app\service\Member\AccountService;
use app\service\Vip\VipUserInfoService;
use code;
use think\Db;

class Payment extends Load
{

    /**
     * 将购物商品和赠品插入到订单商品表 删除购物车中对应商品
     *
     * @param   array $goods_list 商品详情
     * @param   int $new_order_id 订单自增id
     * @param   int $once
     * @param   int $o_status 是否完成支付
     */
    public function insert_order_goods($goods_list, $new_order_id, $bonus = 0.00, $once = 0, $o_status = 0)
    {

        $goods = new Goods();
        $tmp_bonus = 0;
        foreach ($goods_list['goods_detail'] as $item) {
            $goods_bonus = round($bonus * ($item['subtotal'] / $goods_list['subtotal']), 2);
            $tmp_bonus += $goods_bonus;
            $order_goods_insert[] = array(
                'order_id' => $new_order_id,
                'goods_id' => $item['goods_id'],
                'shops_id' => $item['shops_id'],
                'suppliers_id' => $item['suppliers_id'],
                'goods_name' => $item['goods_name'],
                'goods_sn' => $item['goods_sn'],
                'product_id' => $item['product_id'],
                'goods_number' => $item['goods_number'],
                'goods_price' => $item['goods_price'],
                'surplus' => 0,
                'discount' => $item['discount'],
                'bonus' => $goods_bonus, //
                'dikouma' => '',
                'shipping_fee' => 0,
                'o_status' => $o_status,
                'goods_attr' => $item['goods_attr'],
                'is_virtual' => $item['is_virtual'],
                'type' => $item['type'],
                'goods_attr_id' => $item['goods_attr_id'],
                'presale_id' => $item['presale_id'],
            );

            $goods->change_goods_storage($item['sup_id'], $item['product_id'], -$item['goods_number'], $item['presale_id']);

            $rec_ids[] = $item['rec_id'];
        }

        if ($bonus != $tmp_bonus) {
            $order_goods_insert[0]['bonus'] = $order_goods_insert[0]['bonus'] + ($bonus - $tmp_bonus);
        }

        if ($goods_list['present']) {
            foreach ($goods_list['present'] as $item) {
                $order_goods_insert[] = array(
                    'order_id' => $new_order_id,
                    'goods_id' => $item['id'],
                    'shops_id' => $item['shops_id'],
                    'suppliers_id' => $item['supplier'],
                    'goods_name' => $item['name'],
                    'goods_sn' => $item['code'],
                    'product_id' => 0,
                    'goods_number' => $item['goods_number'],
                    'goods_price' => $item['goods_price'],
                    'surplus' => 0,
                    'discount' => 0,
                    'bonus' => 0, //
                    'dikouma' => '',
                    'shipping_fee' => 0,
                    'o_status' => $o_status,
                    'goods_attr' => '',
                    'is_virtual' => 0,
                    'type' => 0,
                    'goods_attr_id' => '',
                    'presale_id' => 0,
                );

                $goods->change_goods_storage($item['goods_id'], '', -$item['goods_number'], '');

            }
        }


        //商品加入order_goods 删除购物车商品
        $this->name('order_goods')->insertAll($order_goods_insert);

        if ($once) {
            $this->name('once_cart')->where('rec_id', 'in', implode(',', $rec_ids))->delete();
        } else {
            $this->name('cart')->where('rec_id', 'in', implode(',', $rec_ids))->delete();
        }

    }


    /* 取得支付列表 */
    public function available_payment_list($user_id, $field = 'pay_id, pay_code, pay_name, pay_fee, pay_desc, pay_config, is_cod,is_online')
    {

        $is_mendian = $this->name('users')->where('user_id', $user_id)->value('is_mendian');
        if (!$is_mendian) {
            $where = [
                'enabled' => 1,
                'pay_id' => ['neq', 14], //门店支付id
            ];
        } else {
            $where = ['enabled' => 1];
        }
        $payment_list = $this->name('payment')->field($field)->where($where)->select();
        foreach ($payment_list as $key => $payment) {
            if (substr($payment['pay_code'], 0, 4) == 'pay_') {
                unset($payment_list[$key]);
                continue;
            }
        }

        return $payment_list;
    }

    /**
     * 统一支付回调
     *
     * @access  public
     * @param   int $order_sn 内部流水号
     * @param   float $total_fee 支付金额
     * @param   int $trade_no 外部流水号
     * @param   string $pay_type 支付方式
     * @param   string $is_dingjin 支付body 用来放定金标识符DJ
     * @param   array $attach 自定义回调参数
     * @return  bool
     */
    public function new_order_paid($order_sn, $total_fee, $trade_no, $pay_type, $is_dingjin, $attach = null)
    {

        if (empty($order_sn)) {
            $this->set_pay_error('', '', 0, $pay_type, '回传订单号为空');
            return false;
        }

        $order_info = $this->name('order_info')->where([['is_delete', '=', 0], ['order_sn', '=', $order_sn]])->find();
        if (empty($order_info)) {
            $this->set_pay_error($order_info['order_id'], $order_sn, 0, $pay_type, '订单不存在，或者已删除');
            return false;
        }
        $order_id = $order_info['order_id'];
        //如果订单不存在则跳过

        //如果订单状态不是等待买家付款则跳过
        if (!in_array($order_info['o_status'], [code::NS_WAIT_P, code::NS_DINGJIN_P])) {
            $this->set_pay_error($order_sn, $order_info['order_sn'], $order_info['order_amount'], code::PS_PAYED, '订单状态错误,原状态为：' . (new Order())->get_ostatus_ch($order_info['o_status']));
            return false;
        }
        Db::startTrans();
        try {
            $update = [
                'o_status' => code::NS_WAIT_D,// 等待卖家发货
                'a_status' => code::AS_PAY,//订单付款
                'pay_time' => time(),
                'p_status' => code::PS_PAYED, //支付成功
                'order_no' => $trade_no
            ];

            $o_status = $update['o_status'];
            /*-记录用户-*/
            $update['pay_time'] = time();
            $update['money_paid'] = Db::raw("money_paid + $total_fee"); /*- 已支付= 已支付(可能是定金和余额) + 本次支付金额 -*/
            $update['order_amount'] = Db::raw("order_amount - $total_fee");/*- 订单总金额= 订单总金额 - 本次支付金额  -*/

            $user = \app\common\Entity\Users::getInfoByUserId($order_info['user_id'], 'is_vip');
            $orderGoods = $this->name('order_goods')->alias('og')
                ->select('sup.id as sup_id,sup.vs_id')
                ->join([
                    ['ecs_goods_shp shp', 'og.goods_id = shp.id', 'left'],
                    ['ecs_goods_sup sup', 'sup.id=shp.goods_id', 'left'],
                ])
                ->where('order_id', $order_info['order_id'])
                ->select()->toArray();

            $vip_goods_info = [];

            foreach ($orderGoods as $row) {
                $vip_goods_info[$row['sup_id']] = $row['vs_id'];
            }

            if (!$user['is_vip']) {
                VipUserInfoService::service()->beVip($vip_goods_info, $order_info['user_id']);
            }

            if ($vip_goods_info) {
                LeaderStatusService::service()->initLeadStatus($order_info['user_id'], $order_id, current(array_keys($vip_goods_info)));
            }


            $this->name('order_info')->where('order_id', $order_info['order_id'])->update($update);
            $this->name('order_goods')->where('order_id', $order_info['order_id'])->update(['o_status' => $o_status]);


            AccountService::service()
                ->setUserId($order_info['user_id'])
                ->setChangeDesc(sprintf('订单%s微信支付', $order_sn))
                ->setChangeType(AccountLogEnums::CHANGE_TYPE_OTHER)
                ->setMoneyType(AccountLogEnums::MONEY_TYPE_FLOW)
                ->setUserMoney(-$total_fee)
                ->Log();

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->set_pay_error($order_id, $order_info['order_amount'], $pay_type, $e->getMessage());
        }
        redis()->lPush(RedisKeyEnums::DISTRIBUTION_QUEUE, $order_info['order_id']);
        return true;
    }


    public function order_action($order_id, $order_status, $shipping_status, $pay_status, $note = '', $place = 0, $user_id = '')
    {
        if ($GLOBALS['user_id']) {
            $user_id = $GLOBALS['user_id'];
        }

        $insert_arr = array(
            'order_id' => $order_id,
            'action_user' => $user_id,
            'order_status' => $order_status,
            'shipping_status' => $shipping_status,
            'pay_status' => $pay_status,
            'action_place' => $place,
            'action_note' => $note,
            'log_time' => time(),
        );

        $this->name('order_action')->insert($insert_arr);
    }

    public function insert_pay_log($id, $amount, $type = code::PAY_SURPLUS, $is_paid = 0)
    {
        $insert = [
            'order_id' => $id,
            'order_amount' => $amount,
            'order_type' => $type,
            'is_paid' => $is_paid
        ];
        $this->name('pay_log')->insert($insert);

    }

    //插入支付错误信息
    public function set_pay_error($order_id, $order_sn, $money, $type = '', $remark)
    {
        $insert_arr = array(
            'order_id' => $order_id,
            'order_sn' => $order_sn,
            'money' => floatval($money),
            'type' => $type,
            'remark' => $remark,
            'add_date' => date('Y-m-d H:i:s'),
        );

        $this->name('pay_errors_new')->insert($insert_arr);
    }


    public function set_pay_finished(string $order_sn): void
    {
        $order_info = $this->name('order_info')->field('order_id,p_status,o_status,d_status,user_id')->where('order_sn', $order_sn)->find();
        if ($order_info) {
            $this->name('order_info')->where('order_id', $order_info['order_id'])->update(['p_status' => code::PS_FINISHED]);
            $this->order_action($order_info['order_id'], $order_info['o_status'], $order_info['d_status'], $order_info['p_status'], '交易3个月后支付宝回调,不能退款', '', $order_info['user_id']);
        }
    }

}