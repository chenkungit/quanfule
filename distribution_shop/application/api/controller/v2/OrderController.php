<?php

namespace app\api\controller\v2;

use app\common\controller\ApiController;
use app\common\Enums\PaymentEnums;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use app\common\model\v2\Cart;
use app\common\model\Order;
use app\common\model\Shipping;
use app\common\model\Users;
use app\dashboard\model\Refund;
use app\service\System\SystemSettingService;
use think\Db;
use think\Request;
use code;

class OrderController extends ApiController
{
    protected $order;
    protected $Message_system;
    protected $Refund;

    public function __construct(Request $request, Order $order, Refund $refund)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
        $this->order = $order;
        $this->Refund = $refund;
    }


    public function checkout_info(Shipping $shipping, Cart $cart, Users $users)
    {
//        $GLOBALS['user_id'] = 276724;
//        return $this->respondWithArray($GLOBALS['user_id']);
        $where = [
            ['user_id', '=', $GLOBALS['user_id']],
            ['checked', '=', 1],
        ];

        $this->data['once'] = empty($this->data['once']) ? 0 : 1;
        $this->data['sendaddress'] = empty($this->data['sendaddress']) ? 0 : $this->data['sendaddress'];
        $this->data['is_dingjin'] = empty($this->data['is_dingjin']) ? 0 : 1;
        $this->data['is_group'] = (empty($this->data['is_group']) || $this->data['is_group'] == 99) ? 0 : $this->data['is_group'];
        if ($this->data['once'] == 1) {

            $this->data['gshp_id'] = empty($this->data['gshp_id']) ? 0 : $this->data['gshp_id'];
            $this->data['number'] = empty($this->data['number']) ? 1 : $this->data['number'];
            $this->data['presale'] = empty($this->data['presale']) ? 0 : explode(',', $this->data['presale']);
            $this->data['attr_id'] = empty($this->data['attr_id']) ? [] : $this->data['attr_id'];
            $this->data['group_id'] = empty($this->data['group_id']) ? 0 : $this->data['group_id'];
            $this->data['delivery_id'] = empty($this->data['delivery_id']) ? '' : $this->data['delivery_id'];
            /*-插入成功直接返回sendaddrss-*/
            $flag = $cart->once_add($this->data['gshp_id'], $this->data['number'], $this->data['attr_id'], $this->data['presale'], $this->data['is_dingjin'], $this->data['is_group'], $this->data['group_id'], $this->data['delivery_id']);

            if ($flag !== true) {
                return $flag;
            }

        }
        if ($this->data['once']) {
            if (!$this->order->name('once_cart')->where($where)->count()) {
                abort(404, '你没有提交任何商品');
            }
        } else {
            if (!$this->order->name('cart')->where($where)->count()) {
                abort(404, '你没有提交任何商品');
            }
        }

        $user_info = $users->user_info('', 'user_money,frozen_money');

        $user_money = $user_info['user_money'];
        $frozen_money = $user_info['frozen_money'];


        $goods_list = $cart->get_cart_goods_checked($this->data['sendaddress'], $this->data['is_dingjin'], $this->data['once'], $this->data['is_group']);

        /*-返回默认地址-*/
        $field = 'ad.address_id,consignee,province,city,district,address,tel';
        $consignee = $this->order->name('user_address')->alias('ad')->field($field)->leftJoin('ecs_users u', 'ad.address_id=u.address_id')->where('u.user_id', $GLOBALS['user_id'])->find();
        if ($consignee) {

            $pcd_id_string = sprintf('%s,%s,%s', $consignee['province'], $consignee['city'], $consignee['district']);

            $province = Db::name('region')->where('region_id', $consignee['province'])->value('region_name');
            $city = Db::name('region')->where('region_id', $consignee['city'])->value('region_name');
            $district_name = Db::name('region')->where('region_id', $consignee['district'])->value('region_name');
            $consignee['province_name'] = $province ? $province : '未知省份';
            $consignee['city_name'] = $city ? $city : '未知市';
            $consignee['district_name'] = $district_name ? $district_name : '未知区';

//            $zxwl_code = $shipping->zxwl($consignee);
//            return $this->respondWithArray($zxwl_code);
//            $shipping_list = $shipping->available_shipping_list($pcd_id_string, $this->data['sendaddress'], array_unique(array_column($goods_list['goods_detail'], 'suppliers_id')));
//            $shipping_list = array_values($shipping_list);
        } else {
            $consignee = [];
            $shipping_list = [];
        }

        //取得用户可用优惠券
        $arr['user_bonus'] = [];
//        $bonus->user_bonus($goods_list, $this->data['is_dingjin'], $this->data['is_group']);
//        if ($shipping_list) {

//            foreach ($shipping_list as $key => $value) {
//                $shipping_list[$key]['shipping_fee'] = 0;
        /*-定金商品不计算运费-*/
//                $shipping_list[$key]['shipping_fee'] = $shipping->shipping_fee($value['shipping_id'], $value['configure'], round($goods_list['subweight'] - $goods_list['freeshipping_subweight'], 3), $goods_list['subtotal'], $goods_list['subnumber']);
//            }
        /*按运费排一下序*/
//            foreach ($shipping_list as $key => $row) {
//                $volume[$key] = $row['shipping_fee'];
//            }
//            var_dump($shipping_list);
//            var_dump($volume);exit;
//            array_multisort($volume, SORT_ASC, $shipping_list);
//        }

        $goods_list['shipping_list'][] = ['shipping_id' => 1, 'shipping_name' => '快递', 'shipping_fee' => 0];

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

        if (SystemSettingService::service()->getWechatPayStatus()) {
            $arr['payment_id'] = PaymentEnums::PAYMENT_TYPE_WECHAT_H5;
        } else {
            $arr['payment_id'] = PaymentEnums::PAYMENT_TYPE_SURPLUS;
        }

        //$arr['payment'] = $payment->available_payment_list($GLOBALS['user_id'],'pay_id,pay_code,pay_name');
        return $this->respondWithArray($arr);
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


    public function chosen_address(Shipping $shipping, Cart $cart)
    {

        $is_dingjin = empty($this->data['is_dingjin']) ? 0 : 1;
        $is_group = empty($this->data['is_group']) ? 0 : 1;
        $once = empty($this->data['once']) ? 0 : 1;

        $goods_list = $cart->get_cart_goods_checked($this->data['sendaddress'], $is_dingjin, $this->data['once'], $is_group);

        /*-返回默认地址-*/
        $field = 'ad.address_id,consignee,province,city,district,address,tel';
        $consignee = $this->order->name('user_address')->alias('ad')->field($field)
            ->where('ad.address_id', $this->data['address_id'])
            ->where('ad.user_id', $GLOBALS['user_id'])->find();

        if ($consignee) {

            $pcd_id_string = sprintf('%s,%s,%s', $consignee['province'], $consignee['city'], $consignee['district']);

            $province = Db::name('region')->where('region_id', $consignee['province'])->value('region_name');
            $city = Db::name('region')->where('region_id', $consignee['city'])->value('region_name');
            $district_name = Db::name('region')->where('region_id', $consignee['district'])->value('region_name');
            $consignee['province_name'] = $province ? $province : '未知省份';
            $consignee['city_name'] = $city ? $city : '未知市';
            $consignee['district_name'] = $district_name ? $district_name : '未知区';

//            $zxwl_code = $shipping->zxwl($consignee);
//            return $this->respondWithArray($zxwl_code);
            $shipping_list = $shipping->available_shipping_list($pcd_id_string, $this->data['sendaddress'], array_unique(array_column($goods_list['goods_detail'], 'suppliers_id')));

            $shipping_list = array_values($shipping_list);

            foreach ($shipping_list as $key => $value) {
                /*-定金商品不计算运费-*/
                if ($is_dingjin || $is_group) {
                    $shipping_list[$key]['shipping_fee'] = 0;
                } else {
                    $shipping_list[$key]['shipping_fee'] = $shipping->shipping_fee($value['shipping_id'], $value['configure'], $goods_list['subweight'] - $goods_list['freeshipping_subweight'], $goods_list['subtotal'], $goods_list['subnumber']);
                }
            }

            /*按运费排一下序*/
            foreach ($shipping_list as $key => $row) {

                $volume[$key] = $row['shipping_fee'];
            }
//            var_dump($shipping_list);
//            var_dump($volume);exit;
            array_multisort($volume, SORT_ASC, $shipping_list);
        } else {
            $consignee = [];
            $shipping_list = [];
        }


        $arr['shipping_list'] = $shipping_list;
        $arr['consignee'] = $consignee;


        return $this->respondWithArray($arr);
    }


    /*申请退货v2 可选数量 商品rec_id和number转为json传递*/
    public function refund()
    {
        /*-补点全部退货代码*/

        $validate = $this->validate($this->data, 'app\api\validate\OrderValidate.refund_v2');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
        $goods_json = json_decode($this->data['goods_json'], true);

        if (!is_array($goods_json)) {
            abort(500, '格式错误');
        }
        //等待卖家发货  卖家发货中 部分发货 发货完成 申请退货 已付定金
        $where = [['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['o_status', 'in', [code::NS_WAIT_D, code::NS_D_ING, code::NS_PART_D, code::NS_FINISH_D, code::NS_DINGJIN_P]]];
        $order_info = $this->order->check_order($where);
        if (empty($order_info)) {
            abort(404, '该订单不存在或状态错误');
        }

        $goods_number_sum = array_sum(
            array_map(function ($val) {
                return $val['number'];
            }, $goods_json)
        );

        $flag = $this->order->check_refund_total_number($this->data['order_id'], $goods_number_sum);

        $reason_img = [];
        if (isset($_FILES['img'])) {
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $img_arr = $upload->multiArrange($_FILES['img']);
            foreach ($img_arr as $item) {
                $reason_img[] = $upload->singleUpload($item, 'refund');
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
                $refund_id = $this->order->name('refund_order')->insertGetId($refund_insert_arr);
                foreach ($goods_json as $item) {
                    $goods = $this->order->name('order_goods')->where('rec_id', $item['rec_id'])->where('order_id', $this->data['order_id'])->find();
                    if (!$goods) {
                        abort(400, '商品不存在');
                    }
                    if ($item['number'] > $goods['goods_number']) {
                        abort(400, '商品超出数量');
                    }

                    //添加退货商品数据
                    $refund_goods_insert_arr[] = [
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
                        'bonus' => $goods['bonus'],
                        'is_virtual' => $goods['is_virtual'],
                        'type' => $goods['type'],
                        'refund_number' => $item['number'],
                        'goods_attr' => $goods['goods_attr'],
                        'goods_attr_id' => $goods['goods_attr_id'],
                        'presale_id' => $goods['presale_id']
                    ];

                }
                $this->order->name('order_goods')->where('rec_id', 'in', array_column($refund_goods_insert_arr, 'rec_id'))->update(['o_status' => code::NS_APPLY_R, 'r_status' => 2]);
                $this->order->name('refund_goods')->insertAll($refund_goods_insert_arr);
            }

            //整单退货
            if ($flag === false) {
                $refund_insert_arr['type'] = 1;
                $this->order->name('order_goods')->where('order_id', $this->data['order_id'])->update(['o_status' => code::NS_APPLY_R, 'r_status' => 2]);
                /*-接入erp后不需要退货单可以删除代码-*/
                $refund_id = Db::name('refund_order')->insertGetId($refund_insert_arr);
                $sql = "INSERT INTO ecs_refund_goods" .
                    " (`refund_id`,`rec_id`,`goods_id`,`shops_id`,`suppliers_id`,`product_id`,`goods_name`,`goods_sn`,`goods_price`,`discount`,`bonus`,`is_virtual`,`type`,`refund_number`,`goods_attr`,`goods_attr_id`,`presale_id`) " .
                    " SELECT '$refund_id',`rec_id`,`goods_id`,`shops_id`,`suppliers_id`,`product_id`,`goods_name`,`goods_sn`,`goods_price`,`discount`,`bonus`,`is_virtual`,`type`,`goods_number`,`goods_attr`,`goods_attr_id`,`presale_id` " .
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
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort(400, $e->getMessage());
        }

        return $this->respondWithArray('', '申请退货成功，请耐心等待客服处理');
    }
}