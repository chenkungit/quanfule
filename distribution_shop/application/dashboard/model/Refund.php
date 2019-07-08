<?php

namespace app\dashboard\model;

use app\common\model\Goods;
use think\Model;
use think\Request;
use code;

class Refund extends Model
{
    protected $data;

    public function __construct($data = [])
    {
        parent::__construct($data);

    }

    public function refund_list()
    {
        $this->data = Request()->param();
        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 15;

        if (!empty($this->data['order_sn'])) $where[] = ['oi.order_sn', 'LIKE', "%" . intval($this->data['order_sn']) . "%"];
        if (!empty($this->data['refund_sn'])) $where[] = ['refund_sn', 'LIKE', "%" . intval($this->data['refund_sn']) . "%"];
        if (!empty($this->data['sendaddress'])) $where[] = ['ro.sendaddress', '=', intval($this->data['sendaddress'])];
        if (!empty($this->data['shipping_id'])) $where[] = ['shipping_id', '=', intval($this->data['shipping_id'])];

        $where[] = ['status', '=', empty($this->data['status']) ? 0 : intval($this->data['status'])];

        $join = [
            ['ecs_order_info oi', 'oi.order_id = ro.order_id', 'left']
        ];

        $res = $this->name('refund_order')->alias('ro')->field('ro.*,oi.msg_to_buyer,oi.label')->join($join)->where($where)->page("$page,$limit")->order('add_time desc')->select();

        $count = $this->name('refund_order')->alias('ro')->join($join)->where($where)->count();
        $pagecount = ceil($count / $limit);

        foreach ($res as &$item) {
            $item['add_time'] = date('Y-m-d H:i:s', $item['add_time']);

            $item['status_name'] = $this->get_rstatus_ch($item['status']);
            $item['type_name'] = $item['type'] == 1 ? '整单退货' : '部分退货';

            if ($item['status'] != code::NRS_CANCLE) {
                $item['can_detail'] = '1';
            }

            $item['reason_type'] = \code::refund_reason[$item['reason_type']];

        }

        return ['item' => $res, 'page_count' => $pagecount, 'record' => $count];
    }

    public function refund_info($refund_id, $all = false)
    {

        $join = [
            ['ecs_order_info oi', 'ro.order_id = oi.order_id', 'inner'],
            ['ecs_users u ', 'oi.user_id = u.user_id', 'inner']
        ];
        if ($all) {
            $fields = "oi.*,ro.*,IF(u.nick!='',u.nick,u.user_name) as nick,u.mobile";
        } else {
            $fields = 'ro.*,oi.order_no,oi.r_status,oi.pay_id,oi.pay_name,oi.province,oi.city,oi.district,oi.address,oi.consignee,oi.phone,oi.shipping_id,oi.shipping_name,oi.money_paid,';
            $fields .= 'oi.goods_amount,oi.shipping_fee,oi.bonus,oi.bonus_ship,oi.surplus,oi.discount,u.nick,u.user_name,u.mobile';
        }

        $res = $this->name('refund_order')->alias('ro')->field($fields)->join($join)->where('id', $refund_id)->find()->toArray();
        $res['total_fee'] = round($res['goods_amount'] + $res['shipping_fee'], 2);
        $res['reason_img'] = json_decode($res['reason_img'], true);
        $res['refund_money_paid'] = 0;//退款给用户的金额(除余额)
        $res['remain_money_paid'] = 0;//回写用户支付的金额(除余额)
        $res['refund_shipping_fee'] = 0;
        $res['remain_shipping_fee'] = $res['shipping_fee'];
        $res['remain_bonus_ship'] = $res['bonus_ship'];
        if (in_array($res['r_status'], [code::NS_WAIT_D, code::NS_D_ING]) && $res['type'] == 1) {
            $res['refund_shipping_fee'] = floatval($res['shipping_fee']) - $res['bonus_ship'];
            $res['remain_shipping_fee'] = $res['shipping_fee'] - $res['refund_shipping_fee'];
            $res['remain_bonus_ship'] = 0;
        }

        $res['status_name'] = code::get_ostatus_ch($res['r_status']);
        $res['add_time'] = date('Y-m-d H:i:s', $res['add_time']);
        $res['operate_time'] = date('Y-m-d H:i:s', $res['operate_time']);
        $pcd_id_string = sprintf('%s,%s,%s', $res['province'], $res['city'], $res['district']);
        $pcd_array = $this->name('region')->where('region_id', 'in', $pcd_id_string)->column('region_name');
        $res['region'] = implode(' ', $pcd_array);

        $order_action = $this->name('order_action')->where([
            ['order_id', '=', $res['order_id']],
            ['action_place', '=', 2]
        ])->order('log_time DESC,action_id DESC')->select();

        $refund_goods_cb = $this->refund_goods($refund_id);
        $res['refund_goods'] = $refund_goods_cb['res'];
        $res['refund_goods_price'] = round($refund_goods_cb['refund_goods_price'],2);
        $res['remain_bonus'] = $res['bonus'] - $refund_goods_cb['refund_bonus'];//回写优惠券金额
        $res['remain_discount'] = $res['discount'] - $refund_goods_cb['refund_discount'];//回写折扣
        $res['refund_bonus'] = round($refund_goods_cb['refund_bonus'],2);//退款扣除的优惠券金额

        $res['refund_discount'] =  $res['type'] == 1 ? $res['discount'] : $refund_goods_cb['refund_discount'];//退款扣除的折扣

        $res['refund_total_fee'] = round($res['refund_goods_price'] + $res['refund_shipping_fee'] - $res['refund_discount'] - $res['refund_bonus'], 2);
        $res['remain_goods_amount'] = $res['goods_amount'] - $res['refund_goods_price'];
        $res['refund_surplus'] = $res['surplus'];
        if ($res['pay_id'] == code::balance) {
            $res['refund_surplus'] = $res['refund_total_fee'];
            $res['remain_surplus'] = round($res['surplus'] - $res['refund_surplus'], 2);
        } else {
            /*-其他方式支付时 退款先退余额 再退会员卡或者第三方的钱-*/
            if ($res['surplus']) {

                /*-主要用于部分退货  退款金额小于退款金额时 -*/
                if ($res['refund_total_fee'] < $res['surplus']) {
                    $res['refund_surplus'] = $res['refund_total_fee'];
                    $res['remain_surplus'] = round($res['surplus'] - $res['refund_surplus'], 2);
                } else {
                    $res['refund_surplus'] = $res['surplus'];
                    $res['remain_surplus'] = 0;
                    $res['refund_money_paid'] = round($res['refund_total_fee'] - $res['surplus'], 2);
                    $res['remain_money_paid'] = $res['money_paid'] - $res['surplus'] - $res['refund_money_paid'];
                }
            }
        }

        if ($order_action) {
            foreach ($order_action as &$item) {
                $item['order_status'] = code::get_ostatus_ch($item['order_status']);
                $item['pay_status'] = code::ps[$item['pay_status']];
                $item['shipping_status'] = code::ss[$item['shipping_status']];
                $item['log_time'] = date('Y-m-d H:i:s', $item['log_time']);
            }
            $res['order_action'] = $order_action;
        }

        return $res;
    }

    public function set_delivery_refund($order_id)
    {
        $update = [
            'status' => code::NDS_FINISH_R,
            'refund_time' => time()
        ];
        $this->name('delivery_order')->where('order_id', $order_id)->update($update);
        (new Goods())->change_order_goods_storage($order_id, false, 1);
    }

    /**-退款完成修改订单状态和订单商品状态
     * @param $refund_info array 订单id
     * -*/
    public function set_order_refund($refund_info)
    {
        $arr = [
            'bonus_id' => 0,
            'bonus' => 0,
            'bonus_ship_id' => 0,
            'shipping_fee' => $refund_info['remain_shipping_fee'],
            'bonus_ship' => $refund_info['remain_bonus_ship'],
            'surplus' => 0,
            'discount' => 0,
            'money_paid' => 0,
            'goods_amount' => 0,
            'order_amount' => 0,
            'o_status' => code::NS_FINISH_R,
            'd_status' => code::SS_UNSHIPPED,
            'p_status' => code::PS_UNPAYED,
            'finish_time' => time(),
            'invoice_no' => ''
        ];
        $this->name('order_info')->where('order_id', $refund_info['order_id'])->update($arr);
        $this->name('order_goods')->where('order_id', $refund_info['order_id'])->update(['o_status' => code::NS_FINISH_R, 'r_status' => 5]);
    }

    public function set_section_order_refund($refund_info)
    {
        /*--修改-*/

        $arr = [
            'shipping_fee' => $refund_info['remain_shipping_fee'],
            'bonus_ship' => $refund_info['remain_bonus_ship'],
            'discount' => $refund_info['remain_discount'],
            'bonus' => $refund_info['remain_bonus'],
            'surplus' => $refund_info['remain_surplus'],
            'money_paid' => $refund_info['remain_money_paid'],
            'goods_amount' => $refund_info['remain_goods_amount'],
            'o_status' => $refund_info['r_status'],
        ];
        foreach ($refund_info['refund_goods'] as $item) {
            if ($item['delete']) {
                $this->name('order_goods')->where('rec_id', $item['rec_id'])->delete();
            } else {
                $this->name('order_goods')->where('rec_id', $item['rec_id'])->update([
                    'goods_number' => $item['goods_number'] - $item['refund_number'],
                    'discount' => $item['discount'] - $item['refund_discount'],
                    'o_status' => $refund_info['r_status']
                ]);
            }
        }
        /**-存在部分发货的订单 退完该次商品状态变成全部发货-*/
        if ($refund_info['r_status'] == code::NS_PART_D) {
            if ($this->name('order_goods')->where('o_status', 'neq', code::NS_FINISH_D)->count()) {
                $arr['o_status'] = code::NS_FINISH_D;
            }
        }
        $this->name('order_info')->where('order_id', $refund_info['order_id'])->update($arr);
    }

    private function refund_goods($refund_id)
    {
        $join = [
            ['ecs_order_goods o', 'o.rec_id = g.rec_id', 'LEFT'],
            ['ecs_shops shps', 'shps.id = g.shops_id', 'LEFT'],
            ['ecs_suppliers sups', 'sups.id = g.suppliers_id', 'LEFT'],
            ['ecs_goods_presale presale', 'presale.id = g.presale_id', 'LEFT'],
        ];
        $where['g.refund_id'] = $refund_id;
        $fields = 'g.*,o.bonus, shps.name as shops_name, sups.name as suppliers_name, o.goods_number, presale.predate';
        $res = $this->name('refund_goods')->alias('g')->field($fields)->join($join)->where($where)->select()->toArray();

        $refund_bonus = 0;
        $refund_discount = 0;
        $refund_goods_price = 0;
        foreach ($res as &$item) {
            $item['refund_goods_price'] = $item['refund_number'] * $item['goods_price'];
            if ($item['refund_number'] < $item['goods_number']) {
                $item['refund_discount'] = round($item['refund_number'] / $item['goods_number'] * $item['discount'], 2);
                $item['refund_bonus'] = round($item['refund_number'] / $item['goods_number'] * $item['bonus'], 2);
                $item['delete'] = FALSE;
            } else {
                $item['refund_discount'] = $item['discount'];
                $item['refund_bonus'] = $item['bonus'];
                $item['delete'] = TRUE;
            }
            $refund_bonus += $item['refund_bonus'];
            $refund_discount += $item['refund_discount'];
            $refund_goods_price += $item['refund_goods_price'];
        }

        return compact('res', 'refund_goods_price', 'refund_discount', 'refund_bonus');

    }

    //获取退货单中文状态
    private function get_rstatus_ch($status)
    {

        if ((string)$status == 'all') {
            return array(
                code::NRS_WAIT_R => '等待处理',
                code::NRS_FINISH_R => '处理完成',
                code::NRS_CANCLE => '取消',
            );
        }

        switch ($status) {
            case code::NRS_WAIT_R   :
                return '等待处理';
                break;
            case code::NRS_FINISH_R :
                return '处理完成';
                break;
            case code::NRS_CANCLE   :
                return '取消';
                break;
            default:
                return '未知状态';
                break;
        }
    }
}