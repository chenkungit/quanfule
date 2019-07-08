<?php

namespace app\api\controller\v1;

use app\common\controller\ApiController;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use app\common\model\Cart;
use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\Shipping;
use app\common\model\Users;
use think\Db;
use think\Request;
use code;

class OrderController extends ApiController
{
    protected $order;

    public function __construct(Request $request, Order $order)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
        $this->order = $order;

    }

    public function order_list($o_status = '-1', $page = '1', $limit = 10)
    {

        $where = [
            ['is_delete', '=', 0],
            ['user_id', '=', $GLOBALS['user_id']],
        ];

        if ($o_status != '-1') {
            //待付款 交易关闭
            if ($o_status == 0) {
                array_push($where,
                    ['o_status', '=', 0],
                    ['is_dingjin', 'neq', 1],
                    ['is_group', 'neq', 1]
                );
            }

            //已付款 等待卖家发货
            if ($o_status == 1) {
                array_push($where,
                    ['o_status', 'in', [1, 2]]
                );

            }

            //部分发货
            if ($o_status == 3) {
                array_push($where,
                    ['o_status', '=', 3]
                );
            }

            //发货完成 待收货
            if ($o_status == 4) {
                array_push($where,
                    ['o_status', '=', 4]
                );
            }

            //退货与退货完成

            if ($o_status == 6) {
//                $where.="  and (o_status=5 or o_status=6) ";
                $refund_list = $this->order->get_user_refund_order($page, $limit);
                return $this->respondWithArray($refund_list);
            }

            //交易完成
            if ($o_status == 7) {
                array_push($where,
                    ['o_status', '=', 7]
                );
            }
            //交易完成待评价
            if ($o_status == 999) {
                array_push($where,
                    ['o_status', '=', 7],
                    ['is_comment', '=', 0],
                    ['finish_time', '>', strtotime("-0 year -1 month -0 day")]
                );
            }
            /*-定金商品 未付首款和未付尾款  未付款和已付定金2个状态-*/
            if ($o_status == 9) {
                array_push($where,
                    ['o_status', 'in', [code::NS_WAIT_P, code::NS_DINGJIN_P]],
                    ['is_dingjin', '=', 1]
                );
            }

            if ($o_status == 10) {
                array_push($where,
                    ['is_group', '=', 1],
                    ['o_status', 'neq', 0]
                );
                array_shift($where);//.....删除
            }

            if ($o_status == 11) {
                array_push($where,
                    ['o_status', '=', code::CROWDFUNDING]
                );
            }

        }

        $order_list = $this->order->get_user_orders($where, $page, $limit);

        return $this->respondWithArray($order_list);
    }

    public function logistics_type()
    {
        $res = Db::name('logistics_type')->select();
        return $this->respondWithArray($res);
    }

    public function order_detail()
    {

        $validate = $this->validate($this->data, 'app\api\validate\OrderValidate.order_detail');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }

        $field = 'oi.order_id,oi.order_sn,oi.shipping_name,oi.shipping_id,oi.shipping_time,oi.shipping_fee,oi.phone,oi.o_status,oi.order_amount,oi.bonus,oi.bonus_ship,oi.discount,oi.is_delete,FROM_UNIXTIME(oi.add_time) as add_time,oi.shipping_time,oi.refund_time,oi.finish_time,oi.sendaddress,oi.pay_time,oi.province,oi.city,oi.district,oi.consignee,oi.address,oi.goods_amount,(oi.goods_amount + oi.shipping_fee  - oi.discount - oi.bonus -oi.bonus_ship) AS total_fee,oi.o_status,oi.p_status,oi.d_status,oi.r_status,oi.is_comment,';
        $field .= 'sd.address as sendaddress_name';
        $join = [
            ['ecs_sendaddress sd', 'oi.sendaddress = sd.id', 'LEFT']
        ];
        $order = $this->order->name('order_info')->alias('oi')->join($join)->field($field)->where([['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']]])->find();


        if (!$order) {
            return json_error('', '该订单不存在', self::INVALID_PARAMETER);
        }

        $order = $this->order->get_order_detail($order);

        return $this->respondWithArray($order);
    }

    public function checkout_info(Shipping $shipping, Cart $cart, Users $users)
    {

        $where = [
            'user_id' => $GLOBALS['user_id'],
            'checked' => 1,
        ];

        $this->data['once'] = empty($this->data['once']) ? 0 : $this->data['once'];
        $this->data['sendaddress'] = empty($this->data['sendaddress']) ? 0 : $this->data['sendaddress'];
        $this->data['is_dingjin'] = empty($this->data['is_dingjin']) ? 0 : $this->data['is_dingjin'];

        if ($this->data['once'] == 1) {

            $this->data['gshp_id'] = empty($this->data['gshp_id']) ? 0 : $this->data['gshp_id'];
            $this->data['number'] = empty($this->data['number']) ? 1 : $this->data['number'];
            $this->data['presale'] = empty($this->data['presale']) ? 0 : explode(',', $this->data['presale']);
            $this->data['attr_id'] = empty($this->data['attr_id']) ? [] : $this->data['attr_id'];
            $this->data['libao'] = empty($this->data['libao']) ? 0 : $this->data['libao'];

            /*-插入成功直接返回sendaddrss-*/
            $flag = $cart->once_add($this->data['gshp_id'], $this->data['number'], $this->data['attr_id'], $this->data['presale'], $this->data['is_dingjin'], $this->data['libao']);

            if ($flag !== true) {
                return $flag;
            }

        }
        if (!$this->order->name('cart')->where($where)->count()) {
            abort(404, '你没有提交任何商品');
        }

        $user_info = $users->user_info($GLOBALS['user_id'], 'user_money,frozen_money');
        $user_money = $user_info['user_money'];
        $frozen_money = $user_info['frozen_money'];

        $field = 'ad.address_id,consignee,province,city,district,address,tel';

        /*-返回默认地址-*/
        $join = [
            ['ecs_users u', 'ad.address_id=u.address_id', 'LEFT']
        ];
        $consignee = $this->order->name('user_address')->alias('ad')->field($field)->join($join)->where(['u.user_id' => $GLOBALS['user_id']])->find();


        if ($consignee) {

            $pcd_id_string = sprintf('%s,%s,%s', $consignee['province'], $consignee['city'], $consignee['district']);
            $pcd_array = Db::name('region')->where(['region_id' => ['in', $pcd_id_string]])->column('region_name');

            $consignee['province_name'] = $pcd_array[0];
            $consignee['city_name'] = $pcd_array[1];
            $consignee['district_name'] = $pcd_array[2];

//            $zxwl_code = $shipping->zxwl($consignee);
//            return $this->respondWithArray($zxwl_code);
            $shipping_list = $shipping->available_shipping_list($pcd_id_string, $this->data['sendaddress'], '');
            $shipping_list = array_values($shipping_list);
        } else {
            $consignee = [];
            $shipping_list = [];
        }

        $goods_list = $cart->get_cart_goods_checked($this->data['sendaddress'], $this->data['is_dingjin']);

        //取得用户可用优惠券
        $arr['user_bonus'] = [];
//        $order_num = 1;

        if ($shipping_list) {
            foreach ($shipping_list as $key => $value) {
                /*-定金商品不计算运费-*/
                if ($this->data['is_dingjin']) {
                    $shipping_list[$key]['shipping_fee'] = 0;
                } else {
                    $shipping_list[$key]['shipping_fee'] = $shipping->shipping_fee($value['shipping_id'], $value['configure'], round($goods_list['subweight'] - $goods_list['freeshipping_subweight'], 3), $goods_list['subtotal'], $goods_list['subnumber']);
                }
            }
            /*按运费排一下序*/
            foreach ($shipping_list as $key => $row) {
                $volume[$key] = $row['shipping_fee'];
            }
            array_multisort($volume, SORT_ASC, $shipping_list);
        }

        $goods_list['shipping_list'] = $shipping_list;

        $arr['default_address'] = $consignee;
        // $arr['shipping_list'] = $shipping_list;
        $arr['order_list'] = $goods_list;
        /*-用户余额-*/
        $arr['user_money'] = $user_money;
        $arr['user_money_format'] = $this->order->price_format($user_money);
        /*-冻结余额-*/
        $arr['frozen_money'] = $frozen_money;
        $arr['frozen_money_format'] = $this->order->price_format($frozen_money);
        /*-可用余额 一开始字段理解错了 不需要减  -*/
        $arr['available_money'] = floatval($user_money);
        $arr['available_money_format'] = $this->order->price_format($user_money);
        //$arr['payment'] = $payment->available_payment_list($GLOBALS['user_id'],'pay_id,pay_code,pay_name');
        return $this->respondWithArray($arr);
    }


    public function chosen_address(Shipping $shipping, Cart $cart)
    {

        $field = 'address_id,consignee,province,city,district,address,tel';

        $is_dingjin = empty($this->data['is_dingjin']) ? 0 : 1;

        $consignee = Db::name('user_address')->field($field)->where('address_id', $this->data['address_id'])->find();

        if ($consignee) {

            $pcd_id_string = sprintf('%s,%s,%s', $consignee['province'], $consignee['city'], $consignee['district']);
            $pcd_array = db('region')->where('region_id', 'in', $pcd_id_string)->column('region_name');

            $consignee['province_name'] = $pcd_array[0];
            $consignee['city_name'] = $pcd_array[1];
            $consignee['district_name'] = $pcd_array[2];

//            $zxwl_code = $shipping->zxwl($consignee);
//            return $this->respondWithArray($zxwl_code);
            $shipping_list = $shipping->available_shipping_list($pcd_id_string, $this->data['sendaddress'], '');

            $shipping_list = array_values($shipping_list);

        } else {
            $arr['consignee'] = [];
            $arr['shipping_list'] = [];
        }

        $goods_list = $cart->get_cart_goods_checked($this->data['sendaddress'], $is_dingjin);

        foreach ($shipping_list as $key => $value) {
            /*-定金商品不计算运费-*/
            if ($is_dingjin) {
                $shipping_list[$key]['shipping_fee'] = 0;
            } else {
                $shipping_list[$key]['shipping_fee'] = $shipping->shipping_fee($value['shipping_id'], $value['configure'], $goods_list['subweight'] - $goods_list['freeshipping_subweight'], $goods_list['subtotal'], $goods_list['subnumber']);
            }
        }
        /*按运费排一下序*/
        foreach ($shipping_list as $key => $row) {
            $volume[$key] = $row['shipping_fee'];
        }
        array_multisort($volume, SORT_ASC, $shipping_list);

        $arr['shipping_list'] = $shipping_list;
        $arr['consignee'] = $consignee;


        return $this->respondWithArray($arr);
    }


    /*申请退货*/
    public function refund()
    {

        $validate = $this->validate($this->data, 'app\api\validate\OrderValidate.refund');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
//        if(!isset($_FILES['img'])){
//            return json_error(NULL,'请上传图片',self::INVALID_PARAMETER);
//        }
        //等待卖家发货  卖家发货中 部分发货 发货完成 申请退货 已付定金
        $where = [['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['o_status', 'in', [code::NS_WAIT_D, code::NS_D_ING, code::NS_PART_D, code::NS_FINISH_D, code::NS_DINGJIN_P, code::NS_FINISH]]];
        $order_info = $this->order->check_order($where);

        if (empty($order_info)) {
            abort(404, '该订单不存在或状态错误');
        }
        $flag = $this->order->check_refund_total_number($this->data['order_id'], $this->data['number']);


        $order_update = [
            'o_status' => code::NS_APPLY_R,
            'r_status' => $order_info['o_status'],
            'refund_time' => time(),
            'refund_reason' => $this->data['refund_info'],
        ];

        $reason_img = [];
        if (isset($_FILES['img'])) {
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $img_arr = $upload->multiArrange($_FILES);
            foreach ($img_arr as $item) {
                $reason_img[] = $upload->singleUpload($_FILES['photo'], 'refund');
            }
        }

        //添加退货单数据
        $refund_insert_arr = [
            'refund_sn' => get_refund_sn(),
            'order_sn' => $order_info['order_sn'],
            'order_id' => $this->data['order_id'],
            'user_id' => $GLOBALS['user_id'],
            'add_time' => time(),
            'sendaddress' => $order_info['sendaddress'],
//            'type'     => 1,
            'status' => '0',
            'goods_status' => $this->data['goods_status'],
            'refund_type' => $this->data['refund_type'],
            'reason_type' => $this->data['reason_type'],
            'reason_info' => $this->data['refund_info'],
            'reason_img' => json_encode($reason_img),
        ];

        if ($this->data['refund_type'] == 2) {
            if (!isset($this->data['shipping_id'])) {
                return json_error(NULL, '请选择快递方式', self::INVALID_PARAMETER);
            }
            if (!isset($this->data['refund_invoince_no'])) {
                return json_error(NULL, '请补充快递单号', self::INVALID_PARAMETER);
            }
            $refund_insert_arr['shipping_id'] = $this->data['shipping_id'];
            $refund_insert_arr['refund_invoince_no'] = $this->data['refund_invoince_no'];
        }
        Db::startTrans();
        try {
            //部分退货
            if ($flag === true) {
                $refund_insert_arr['type'] = 2;

                $goods = $this->order->name('order_goods')->where('rec_id', $this->data['rec_id'])->find();

                if ($this->data['number'] > $goods['goods_number']) {
                    abort(400, '商品超出数量');
                }
                $refund_id = $this->order->name('refund_order')->insertGetId($refund_insert_arr);
                //添加退货商品数据
                $refund_goods_insert_arr = [
                    'refund_id' => $refund_id,
                    'rec_id' => $goods['rec_id'],
                    'goods_id' => $goods['goods_id'],
                    'shops_id' => $goods['shops_id'],
                    'suppliers_id' => $goods['suppliers_id'],
                    'product_id' => $goods['product_id'],
                    'goods_name' => $goods['goods_name'],
                    'goods_sn' => $goods['goods_sn'],
                    'goods_price' => $goods['goods_price'],
                    'discount' => $goods['discount'],
                    'is_virtual' => $goods['is_virtual'],
                    'type' => $goods['type'],
                    'refund_number' => $this->data['number'],
                    'goods_attr' => $goods['goods_attr'],
                    'goods_attr_id' => $goods['goods_attr_id'],
                    'presale_id' => $goods['presale_id']
                ];
                $this->order->name('refund_goods')->insert($refund_goods_insert_arr);
                $this->order->name('order_goods')->where('rec_id', $this->data['rec_id'])->update(['o_status' => code::NS_APPLY_R, 'r_status' => 2]);
            }

            //整单退货
            if ($flag === false) {
                $refund_insert_arr['type'] = 1;
                $refund_id = Db::name('refund_order')->insertGetId($refund_insert_arr);
                Db::name('order_info')->where('order_id', $this->data['order_id'])->update($order_update);
                Db::name('order_goods')->where('order_id', $this->data['order_id'])->update(['o_status' => code::NS_APPLY_R, 'r_status' => 2]);
                $sql = "INSERT INTO ecs_refund_goods" .
                    " (`refund_id`,`rec_id`,`goods_id`,`shops_id`,`suppliers_id`,`product_id`,`goods_name`,`goods_sn`,`goods_price`,`discount`,`is_virtual`,`type`,`refund_number`,`goods_attr`,`goods_attr_id`,`presale_id`) " .
                    " SELECT '$refund_id',`rec_id`,`goods_id`,`shops_id`,`suppliers_id`,`product_id`,`goods_name`,`goods_sn`,`goods_price`,`discount`,`is_virtual`,`type`,`goods_number`,`goods_attr`,`goods_attr_id`,`presale_id` " .
                    " FROM ecs_order_goods WHERE order_id = " . $this->data['order_id'];
                Db::execute($sql);
            }

            $order_update = [
                'o_status' => code::NS_APPLY_R,
                'r_status' => $order_info['o_status'],
                'refund_time' => time(),
                'refund_reason' => $this->data['refund_info'],
            ];
            //修改订单状态为申请退款
            $this->order->name('order_info')->where('order_id', $this->data['order_id'])->update($order_update);
            //添加订单做操日志
            $this->order->order_action($this->data['order_id'], code::NS_APPLY_R, $order_info['d_status'], $order_info['p_status'], $this->data['refund_info'], $GLOBALS['user_id']);
            //添加退货单操作日志
            $this->order->order_action($this->data['order_id'], code::NS_APPLY_R, $order_info['d_status'], $order_info['p_status'], $this->data['refund_info'], $GLOBALS['user_id'], 2);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort(400, $e->getMessage());
        }

        return $this->respondWithArray(null, '申请退货成功，请耐心等待客服处理');
    }

    public function tracking()
    {
        $validate = $this->validate($this->data, 'app\api\validate\OrderValidate.tracking');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
        $where = [['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['o_status', 'in', [code::NS_FINISH_D, code::NS_PART_D, code::NS_FINISH]]];
        $order_info = $this->order->check_order($where);
        if (empty($order_info)) {
            return json_error('', '该订单不存在或状态错误', self::INVALID_PARAMETER);
        }

        $province = Db::name('region')->where('region_id', $order_info['province'])->value('region_name');
        $city = Db::name('region')->where('region_id', $order_info['city'])->value('region_name');
        $district_name = Db::name('region')->where('region_id', $order_info['district'])->value('region_name');

        $consignee = [
            'province_name' => $province,
            'city_name' => $city,
            'district_name' => $district_name,
            'consignee' => $order_info['consignee'],
            'address' => $order_info['address'],
            'phone' => $order_info['phone']
        ];

        $delivery = $this->order->name('delivery_invoice')->where([['order_id', '=', $this->data['order_id']]])->order("consign_time asc")->select();

        if (count($delivery)) {

            foreach ($delivery as &$item) {
                if ($item['is_part_sync']) {
                    if ($item['is_part_sync']) {
                        $goods_name = $this->order->name('order_goods')->where('rec_id', 'in', explode(',', $item['rec_ids']))->column('goods_name');
                        $item['rec_ids'] = implode(',', $goods_name);
                    }
                }
//                $msg = express::tracking($item['invoice_no'], express::switch_shipping($item['logistics_name']));

                if (isset($msg['success'])) {
                    $item['invoice_msg'] = [];
                } else {
                    $item['invoice_msg'] = [];
                }
            }
        }

        return $this->respondWithArray(['consignee' => $consignee, 'delivery' => $delivery]);
    }

    public function cancel_refund()
    {

        $validate = $this->validate($this->data, 'app\api\validate\OrderValidate.cancel_refund');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }

        $where = [['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['o_status', '=', code::NS_APPLY_R]];
        $order_info = $this->order->check_order($where);
        if (empty($order_info)) {
            return json_error('', '该订单不存在或状态错误', self::INVALID_PARAMETER);
        }

        $update_order_arr = array(
            'o_status' => $order_info['r_status'],
            'r_status' => 0,
            'refund_time' => 0,
            'refund_reason' => '',
        );

        Db::startTrans();
        try {
            $this->order->name('order_info')->where('order_id', $this->data['order_id'])->update($update_order_arr);
            $refund_id = $this->order->name('refund_order')->where('order_id', $this->data['order_id'])->order('id desc')->value('id');
            $this->order->name('refund_order')->where('id', $refund_id)->update(['status' => code::NRS_CANCLE]);
            $this->order->name('order_goods')->alias('og')->leftJoin('ecs_refund_goods rg', 'og.rec_id=rg.rec_id')
                ->where('rg.refund_id', $refund_id)->update(['o_status' => $order_info['r_status'], 'r_status' => 1]);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort(400, $e->getMessage());
        }


        return $this->respondWithArray(null, '取消退货成功');
    }

    public function affirm_received()
    {

        $where = [['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['o_status', '=', code::NS_FINISH_D]];
        $order_info = $this->order->check_order($where);
        if (empty($order_info)) {
            return json_error('', '该订单不存在或状态错误', self::INVALID_PARAMETER);
        }

        $update_order_arr = [
            'o_status' => code::NS_FINISH,
            'd_status' => code::SS_RECEIVED,
            'finish_time' => time()
        ];

        try {
            $this->order->name('order_info')->where('order_id', $this->data['order_id'])->update($update_order_arr);
            /**-将订单商品中不是退货的商品全部改成发货完成-*/
            $this->order->name('order_goods')->where('order_id', $this->data['order_id'])->where('o_status', 'neq', code::NS_FINISH_R)->update(['o_status' => code::NS_FINISH]);
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }

        return $this->respondWithArray(null, '确定收货成功');
    }


    public function close(Users $users, Goods $goods)
    {

        $validate = $this->validate($this->data, 'app\api\validate\OrderValidate.cancel');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }

        $where = [['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['o_status', '=', code::NS_WAIT_P], ['is_delete', '=', 0]];

        $order = $this->order->check_order($where);

        if (empty($order)) {
            return json_error('', '该订单不存在或状态错误', self::INVALID_PARAMETER);
        }


        $update = [
            'is_delete' => 1,
            'bonus_id' => 0,
            'bonus' => 0,
            'bonus_ship_id' => 0,
            'bonus_ship' => 0,
            'surplus' => 0
        ];

        try {
            $this->order->name('order_info')->where('order_id', $this->data['order_id'])->update($update);

            /*- 恢复库存 -*/
            $goods->change_order_goods_storage($order['order_id'], false, 1);

            /*-存在余额 取消订单 解冻余额 恢复库存-*/
            if (floatval($order['surplus'])) {
                $update_user = [
                    'user_money' => ['exp', 'user_money+' . $order['surplus']]
                ];
                $users->name('users')->where('user_id', $GLOBALS['user_id'])->update($update_user);
            }

        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }
        return $this->respondWithArray('', '关闭订单成功');

    }

    public function delete(Users $users)
    {

        $validate = $this->validate($this->data, 'app\api\validate\OrderValidate.cancel');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }

        $where = [['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['is_delete', '=', 1]];

        $order = $this->order->check_order($where);

        if (empty($order)) {
            return json_error('', '该订单不存在或状态错误', self::INVALID_PARAMETER);
        }

        try {
            $this->order->name('order_info')->where('order_id', $this->data['order_id'])->delete();
            $this->order->name('order_goods')->where('order_id', $this->data['order_id'])->delete();
            /* 解冻余额、积分、红包 */
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }
        return $this->respondWithArray('', '删除订单成功');

    }


}