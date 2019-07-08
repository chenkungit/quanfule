<?php

namespace app\common\model;

use app\common\Utils\PriceHelper;
use code;

class Order extends Load
{
    const NS_WAIT_P = 0; //等待买家付款
    const NS_WAIT_D = 1; //等待卖家发货
    const NS_D_ING = 2; //卖家发货中
    const NS_PART_D = 3; // 部分发货
    const NS_FINISH_D = 4; // 发货完成
    const NS_APPLY_R = 5; // 申请退货
    const NS_FINISH_R = 6; // 退货完成
    const NS_FINISH = 7; // 确定收货
    const NS_CLOSE = 100; // 订单关闭
    const NS_DINGJIN_P = 9; // 已付定金

    const CLODE_TIME = 3600;//订单自动关闭的时间

    public function check_order($where, $field = '*')
    {
        return $this->name('order_info')->field($field)->where($where)->find();
    }


    public function get_user_orders($where = '', $page = 1, $limit = 10)
    {
        $res = [];
        $field = 'order_id,order_sn,surplus,shipping_time,shipping_name,o_status,order_amount,is_delete,is_comment,add_time,consignee,order_amount,goods_amount,shipping_fee,bonus,bonus_ship,discount, (goods_amount + shipping_fee-discount - bonus - bonus_ship) AS total_fee,is_comment';
        $arr = $this->name('order_info')->field($field)->where($where)->page("$page,$limit")->order('add_time DESC')->select();

        foreach ($arr as &$row) {

            $goods = $this->order_goods($row['order_id']); /*-订单商品详情-*/

            $ostatus = $this->get_ostatus_ch($row['o_status'], $row['is_delete']);/*-订单状态中文-*/

            if ($row['o_status'] == code::NS_WAIT_P || $row['o_status'] == code::NS_DINGJIN_P) {

            } else {
                /*-app显示问题-*/
                $row['is_dingjin'] = 0;
            }
            //是否需要查看物流
            $wuliu = 0;
            if ($row['o_status'] == code::NS_FINISH_D || $row['o_status'] == code::NS_PART_D && $row['is_delete'] != 1) {
                $wuliu = 1;
            }

            if ($row['o_status'] == code::NS_PART_D) {
                foreach ($goods as &$item) {
                    $item['send_number'] = $this->get_goods_send($item['order_id'], $item['goods_id']);
                }
            }

            if ($row['o_status'] == code::NS_APPLY_R || $row['o_status'] == code::NS_FINISH_R) {
                /*- 0*/
                $row['status'] = $this->name('refund_order')->where('order_id', $row['order_id'])->order('id desc')->value('status');
            }

            $res[] = [
                'order_id' => $row['order_id'],
                'order_sn' => $row['order_sn'],
                'goods_sum' => $this->order_goods_sum($row['order_id']),
                'order_time' => date('Y-m-d H:i:s', $row['add_time']),
                'order_status' => $ostatus,
                'goods_amount' => $this->price_format($row['goods_amount']),
                'shipping_fee' => $this->price_format($row['shipping_fee']),
                'discount' => $this->price_format($row['discount']),
                'bonus' => $this->price_format($row['bonus']),
                'bonus_ship' => $this->price_format($row['bonus_ship']),
                'total_fee' => $this->price_format($row['total_fee']),
                'is_delete' => $row['is_delete'],
                'goods_list' => $goods,
                'consignee' => $row['consignee'],
                'wuliu' => $wuliu,
                'pay_beg_time' => isset($dj_time) ? $dj_time['pay_beg_time'] : '1970/01/01 00:00:00',
                'pay_end_time' => isset($dj_time) ? $dj_time['pay_end_time'] : '1970/01/01 00:00:00',
                'o_status' => $row['o_status'],
                'order_amount' => $row['order_amount'],
                'group' => isset($row['group']) ? $row['group'] : null,
                'is_comment' => $row['is_comment'],
                'shipping_time' => $row['shipping_time'] ? date('Y-m-d H:i:s', $row['shipping_time']) : '',
                'status' => isset($row['status']) ? $row['status'] : 2
            ];

        }

        return $res;
    }

    public function get_user_dingjin_order($page, $limit)
    {

        $where = [
            'user_id' => $GLOBALS['user_id'],
            'is_dingjin' => 1
        ];

        $res = [];
        $field = 'order_id,order_sn,shipping_name,o_status,dj_status,order_amount,is_delete,is_comment,add_time,consignee,goods_amount,shipping_fee,bonus,bonus_ship,discount, (goods_amount + shipping_fee-discount - bonus - bonus_ship) AS total_fee,is_dingjin';
        $arr = $this->name('order_info')->field($field)->where($where)->page("$page,$limit")->order('add_time DESC')->select();
        foreach ($arr as &$row) {

            $goods = $this->order_goods($row['order_id']);


            if ($row['dj_status'] == 0) {
                $ostatus = '待付定金';
            } else {
                $ostatus = '待付尾款';
            }

            $res[] = [
                'order_id' => $row['order_id'],
                'order_sn' => $row['order_sn'],
                'goods_sum' => $this->order_goods_sum($row['order_id']),
                'order_time' => date('Y-m-d H:i:s', $row['add_time']),
                'order_status' => $ostatus,
                'goods_amount' => $this->price_format($row['goods_amount']),
                'shipping_fee' => $this->price_format($row['shipping_fee']),
                'discount' => $this->price_format($row['discount']),
                'bonus' => $this->price_format($row['bonus']),
                'bonus_ship' => $this->price_format($row['bonus_ship']),
                'total_fee' => $this->price_format($row['total_fee']),
                'is_delete' => $row['is_delete'],
                'goods_list' => $goods,
                'consignee' => $row['consignee'],
                'wuliu' => 0,
                'pay_beg_time' => '',
                'pay_end_time' => '',
                'o_status' => $row['o_status'],
            ];

        }


    }

    public function get_user_refund_order($page, $limit)
    {
        $res = [];

        $where = [
            ['user_id', '=', $GLOBALS['user_id']],
            ['status', 'in', [0, 1]]
        ];
        $arr = $this->name('refund_order')->where($where)->page("$page,$limit")->order('status ASC,refund_sn DESC')->select();

        foreach ($arr as $row) {

            $goods = $this->order_goods($row['id'], 1);

            $res[] = [
                'refund_id' => $row['id'],
                'order_id' => $row['order_id'],
                'refund_sn' => $row['refund_sn'],
                'goods_sum' => $this->order_goods_sum($row['id'], 1),
                'operate_time' => empty($row['operate_time']) ? '' : date('Y-m-d H:i:s', $row['operate_time']),
                'status' => $row['status'],
                'o_status' => 6,
                'goods_list' => $goods,
                'pay_beg_time' => "1970/01/01 00:00:00",
                'pay_end_time' => "1970/01/01 00:00:00",
                'dj_price' => '0',
                'wk_price' => '0'
            ];

        }
        return $res;

    }

    /**
     *  获取指订单的详情
     *
     * @access  public
     * @param   array $order 订单ID
     * @return   array        $order          订单所有信息的数组
     */
    public function get_order_detail($order)
    {


        $pcd_id_string = sprintf('%s,%s,%s', $order['province'], $order['city'], $order['district']);
        $pcd_array = $this->name('region')->where('region_id', 'in', $pcd_id_string)->column('region_name');

        $order['province_name'] = $pcd_array[0];
        $order['city_name'] = $pcd_array[1];
        $order['district_name'] = $pcd_array[2];


        /* 对发货号处理 */
        if (in_array($order['d_status'], [code::SS_SHIPPED, code::SS_RECEIVED, code::SS_SHIPPED_PART, code::OS_SHIPPED_PART])) {
            $order['wuliu'] = 1;
        } else {
            $order['wuliu'] = 0;
        }

        $order['goods_amount'] = PriceHelper::format($order['goods_amount']);
        $order['bonus'] = PriceHelper::format($order['bonus']);
        $order['bonus_ship'] = PriceHelper::format($order['bonus_ship']);
        $order['discount'] = PriceHelper::format($order['discount']);
        $order['difference'] = PriceHelper::format($this->difference($order['order_id']));
        $order['total_fee_format'] = PriceHelper::format($order['total_fee']);
        /*  只有等待买家付款才允许用户修改订单地址 ，生成支付按钮 */
        if ($order['o_status'] == code::NS_WAIT_P && $order['is_delete'] == 0) {

            $order['allow_update_address'] = 1; //允许修改收货地址

        } else {
            $order['allow_update_address'] = 0;
        }

        /* 获取订单中实体商品数量 */
        $order_goods = $this->order_goods($order['order_id'], 0, true);
        /*-返回部分发货中已发货的商品数量-*/
        if ($order['o_status'] == code::NS_PART_D) {
            foreach ($order_goods as &$item) {
                $item['send_number'] = $this->get_goods_send($item['order_id'], $item['goods_id']);
            }
        }

        if ($order['o_status'] == code::NS_APPLY_R) {
            foreach ($order_goods as $key => $value) {

                $where = [
                    'o.order_id' => $order['order_id'],
                    'o.user_id' => $GLOBALS['user_id'],
                    'o.status' => code::NRS_WAIT_R,
                    'g.rec_id' => $value['rec_id']
                ];
                $refund_number = $this->name('refund_goods')->alias('g')->join('ecs_refund_order o', 'o.id = g.refund_id', 'LEFT')
                    ->where($where)->value('g.refund_number');
                $order_goods[$key]['refund_number'] = $refund_number ? $refund_number : 0;
            }
        }

        if ($order['o_status'] == code::NS_FINISH_R || $order['o_status'] == code::NS_APPLY_R) {
            /*- 0*/
            $order['status'] = $this->name('refund_order')->where('order_id', $order['order_id'])->order('id desc')->value('status');
        }
        $order['exist_real_goods'] = $order_goods;
        /* 无配送时的处理 */
        $order['shipping_id'] == -1 and $order['shipping_name'] = '';

        /* 确认时间 支付时间 发货时间 */
        if ($order['pay_time'] > 0 && $order['o_status'] != code::NS_WAIT_P) {
            $order['pay_time'] = date('Y-m-d H:i:s', $order['pay_time']);
        } else {
            $order['pay_time'] = '';
        }
        if ($order['shipping_time'] > 0 && in_array($order['d_status'], [code::SS_SHIPPED, code::SS_RECEIVED])) {
            $order['shipping_time'] = date('Y-m-d H:i:s', $order['shipping_time']);
        } else {
            $order['shipping_time'] = '';
        }

        return $order;
    }


    //根据退货总数以及订单商品总数来判断是不是整单退货
    public function check_refund_total_number($order_id, $refund_total)
    {
        $goods_number = $this->name('order_goods')->where('order_id', $order_id)->where('goods_price', '>', 0)->sum('goods_number');

        if ($goods_number) {
            if ($refund_total < $goods_number) {
                return true;  //部分退货
            }
            if ($refund_total >= $goods_number) {
                return false;
            }
        } else {
            abort(404, '该商品不存在!');
        }
    }


    /*-获取订单物品详情-*/
    private function order_goods($order_id, $choice = 0, $origin = false)
    {


        $join = [
            ['ecs_goods_shp shp', 'og.goods_id = shp.id', 'LEFT'],
            ['ecs_goods_sup sup', 'sup.id = shp.goods_id', 'LEFT'],
            ['ecs_shops spp', 'spp.id = shp.shops_id', 'LEFT'],
            ['ecs_goods_presale presale', 'presale.id = og.presale_id', 'LEFT'],
            ['products p', 'og.product_id = p.product_id', 'LEFT']
        ];

        if ($choice == 1) {
            $table = 'refund_goods';
            $where[] = ['og.refund_id', '=', $order_id];
            $field = 'og.*,IF(p.price,p.price,sup.price) as market_price,og.goods_attr,shp.goods_id as gid,' . concat_img('sup.img', 'img') . ',shp.shops_id,spp.name as ssname, presale.predate';
        } else {
            $table = 'order_goods';
            $field = 'og.*,IF(p.price,p.price,sup.price) as market_price,og.goods_attr,shp.goods_id as gid,' . concat_img('sup.img', 'img') . ',shp.shops_id,spp.name as ssname, presale.predate';
            $where[] = ['og.order_id', '=', $order_id];
        }

        $res = $this->name($table)->alias('og')->field($field)->join($join)->where($where)->select();

        if ($choice == 0) {
            foreach ($res as &$item) {
                if ($origin == false) {
                    $item['goods_price'] = (string)(round($item['goods_price'] - $item['bonus'] / $item['goods_number'] - $item['discount'] / $item['goods_number'], 2));
                }
                $item['market_price'] = (string)$item['market_price'];
                $item['goods_price_format'] = PriceHelper::format($item['goods_price']);
                $item['market_price_format'] = PriceHelper::format($item['market_price']);
                $item['bonus'] = round($item['bonus'], 2);
            }
        }

        return $res;
    }

    public function difference($order_id)
    {
        $join = [
            ['ecs_goods_shp shp', 'og.goods_id = shp.id', 'LEFT'],
            ['ecs_goods_sup sup', 'sup.id = shp.goods_id', 'LEFT'],
        ];
        $fields = '(sup.price - og.goods_price) * og.goods_number as difference';

        $x = $this->name('order_goods')->alias('og')->field($fields)->where('og.order_id', $order_id)->join($join)->find();
        return $x['difference'];
    }


    private function order_goods_sum($order_id, $is_refund = 0)
    {

        if ($is_refund) {
            return $this->name('refund_goods')->where('refund_id', $order_id)->sum('refund_number');
        }
        return $this->name('order_goods')->where('order_id', $order_id)->sum('goods_number');
    }

    private function get_goods_send($order_id, $goods_id)
    {

        $where = [
            ['o.order_id', '=', $order_id],
            ['g.goods_id', '=', $goods_id]
        ];

        $join = [
            ['ecs_delivery_order o', 'o.delivery_id = g.delivery_id', 'LEFT']
        ];

        $number = $this->name('delivery_goods')->alias('g')->join($join)->where($where)->sum('g.send_number');

        return intval($number);
    }


    //获取订单中文状态
    public function get_ostatus_ch($o_status, $del = 0)
    {
        if (!empty($del)) {
            return "交易关闭";
        }

        switch ($o_status) {
            case code::NS_WAIT_P   :
                return '等待买家付款';
                break;
            case code::NS_WAIT_D   :
                return '等待卖家发货';
                break;
            case code::NS_D_ING    :
                return '卖家配货中';
                break;
            case code::NS_PART_D   :
                return '部分发货';
                break;
            case code::NS_FINISH_D :
                return '发货完成';
                break;
            case code::NS_APPLY_R  :
                return '申请退货';
                break;
            case code::NS_FINISH_R :
                return '退货完成';
                break;
            case code::NS_FINISH   :
                return '交易成功';
                break;
            case code::NS_CLOSE    :
                return '交易关闭';
                break;
            case code::NS_DINGJIN_P    :
                return '待付尾款';
                break;
            case code::GROUP         :
                return '拼团中';
                break;
            case code::CROWDFUNDING  :
                return '众筹中';
                break;
            default:
                return '未知状态';
                break;
        }
    }


    /**
     * 记录订单操作记录
     *
     * @access  public
     * @param   string $order_id 订单编号
     * @param   integer $order_status 订单状态
     * @param   integer $shipping_status 配送状态
     * @param   integer $pay_status 付款状态
     * @param   string $note 备注
     * @param   string $username 用户名，用户自己的操作则为 buyer
     * @return  void
     */
    public function order_action($order_id, $order_status, $shipping_status, $pay_status, $note = '', $username = null, $place = 0)
    {
        if (is_null($username)) {
            $username = $GLOBALS['user_name'];
        }

        $insert_arr = array(
            'order_id' => $order_id,
            'action_user' => $username,
            'order_status' => $order_status,
            'shipping_status' => $shipping_status,
            'pay_status' => $pay_status,
            'action_place' => $place,
            'action_note' => $note,
            'log_time' => time(),
        );

        $this->name('order_action')->insert($insert_arr);

    }


}