<?php

namespace app\api\controller\v2;

use app\common\controller\ApiController;
use app\common\Enums\AccountLogEnums;
use app\common\Enums\PaymentEnums;
use app\common\Enums\RedisKeyEnums;
use app\common\Factory\PaymentFactory;
use app\common\model\v2\Cart;
use app\common\model\Order;
use app\common\model\Shipping;
use app\service\Development\DevelopmentStatusService;
use app\service\Leader\LeaderStatusService;
use app\service\Member\AccountService;
use app\service\Member\MemberService;
use app\service\Vip\VipUserInfoService;
use Omnipay\Omnipay;
use Omnipay\WechatPay\BaseAbstractGateway;
use think\Db;
use think\Request;
use code;
use app\common\model\Payment;


class PaymentController extends ApiController
{

    protected $order;
    protected $payment;

    const none = 0;//默认支付方式
    const alipay_id = 4; //支付宝支付
    const alipay_h5_id = 5;
    const wxpay_id = 6;  //微信支付
    const wxpay_h5_id = 7;//微信h5支付
    const balance = 1;   //余额支付
    const card_pay = 8;  //会员卡支付

    protected $mendian;
    /*发票 结构体 默认属性*/
    protected $invoice = [
        'user_invoice_id' => 0,
    ];

    /*-优惠券 结构体-*/
    protected $bonus = [
        'bonus_id' => 0,
        'bonus' => 0,
        'bonus_ship_id' => 0,
        'bonus_ship' => 0,
    ];
    /*-团购 结构体 默认属性-*/
    protected $group = [
        'is_group' => 0,
        'group_id' => 0
    ];


    public function __construct(Request $request, Order $order, Payment $payment)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
        $this->order = $order;
        $this->payment = $payment;
    }

    public function done(Cart $cart, Shipping $shippingF)
    {
        $validate = $this->validate($this->data, 'app\api\validate\PaymentValidate.done');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
        $finish = false;//余额支付状态
        $payed = false;//事务结束后余额支付
        $this->data['openid'] = isset($this->data['openid']) ? $this->data['openid'] : '';
        $this->data['bonus_id'] = isset($this->data['bonus_id']) ? intval($this->data['bonus_id']) : '';
        $this->data['bonus_ship_id'] = isset($this->data['bonus_ship_id']) ? intval($this->data['bonus_ship_id']) : '';
        $this->data['card_number'] = isset($this->data['card_number']) ? $this->data['card_number'] : 0;
        $this->data['is_dingjin'] = 0;
        $this->data['is_group'] = 0;
        $this->data['is_balance'] = empty($this->data['is_balance']) ? 0 : $this->data['is_balance'];
        $this->data['sendaddress'] = empty($this->data['sendaddress']) ? 0 : $this->data['sendaddress'];
        $this->invoice['user_invoice_id'] = empty($this->data['user_invoice_id']) ? 0 : $this->data['user_invoice_id'];/*-个人开票信息id  还未验证准确性-*/
        $this->data['once'] = empty($this->data['once']) ? 0 : 1;
        $this->data['pay_id'] = empty($this->data['pay_id']) ? PaymentEnums::PAYMENT_TYPE_SURPLUS : $this->data['pay_id'];
        $form_id = isset($this->data['form_id']) ? $this->data['form_id'] : null;
        /*--*/
        if ($this->data['pay_id'] == PaymentEnums::PAYMENT_TYPE_SURPLUS) {
            $userTreasure = MemberService::service()->getTreasure($this->data['user_id']);;
            $user_money = $userTreasure['user_money'];

            if ($user_money == 0) {
                return json_error('', '您的可用积分为0', self::INVALID_PARAMETER);
            }
        }

        $consignee = Db::name('user_address')->where([['address_id', '=', $this->data['address_id']], ['user_id', '=', $GLOBALS['user_id']]])->find();

        if (!$consignee) {
            abort(404, '地址不存在');
        }

//        if (empty($this->data['shipping_id'])) {
//            return json_error('', '请选择快递方式', self::INVALID_PARAMETER);
//        }
//        $shipping = Db::name('shipping')->where('shipping_id', $this->data['shipping_id'])->find();
//        if (!$shipping) {
//            abort(404, '快递方式不存在');
//        }

        $goods_list = $cart->get_cart_goods_checked($this->data['sendaddress'], $this->data['is_dingjin'], $this->data['once'], $this->data['is_group']);

        if (!isset($goods_list['goods_detail'])) {
            abort(404, '商品已交易或失效');
        }
        $pcd_id_string = sprintf('%s,%s,%s', $consignee['province'], $consignee['city'], $consignee['district']);

        $ship_fee = 0;
//        if ($goods_list['goods_detail'][0]['is_dingjin'] || $goods_list['goods_detail'][0]['is_group']) {
//            $ship_fee = (int)0;     /*-定金团购众筹支付不计算运费-*/
//        } else {
//            $ship_fee = $shippingF->chosen_shipping_fee($pcd_id_string, $this->data['sendaddress'], $this->data['shipping_id'], round($goods_list['subweight'] - $goods_list['freeshipping_subweight'], 3), $goods_list['subtotal'], $goods_list['subnumber']);
//        }

        /*-应付金额 商品金额(已减折扣)+运费-优惠券-运费劵 --*/
        $order_amount = $goods_list['subtotal'] + $ship_fee - $this->bonus['bonus'] - $this->bonus['bonus_ship'];

        /*-余额大于订单 支付方式为余额支付-*/
        if ($this->data['pay_id'] == PaymentEnums::PAYMENT_TYPE_SURPLUS) {

            if ($order_amount <= $user_money) {
                $finish = true;
            } else {
                return json_error(NULL, '积分余额不足');
            }
        }

        if (!PaymentEnums::PAYMENT_TYPE_MAP[$this->data['pay_id']]) {
            abort(404, '不存在该支付方式');
        }

        if (empty($this->data['pay_id']) && $order_amount > 0) {
            return json_error(NULL, '请选择支付方式');
        }

        Db::startTrans();
        try {
            $this->submit_lock(__CLASS__, 1);
            $order_sn = build_order_no();

            /*-存在使用完优惠券后订单金额为0的情况 不调起在线支付  不对用户余额进行操作-*/
            $order_info = [
                'order_sn' => $order_sn,
                'user_id' => $GLOBALS['user_id'],
                'sendaddress' => $goods_list['sendaddress'],
                'consignee' => $consignee['consignee'],
                'country' => $consignee['country'],
                'province' => $consignee['province'],
                'city' => $consignee['city'],
                'district' => $consignee['district'],
                'address' => $consignee['address'],
                'phone' => $consignee['tel'],
                'shipping_id' => 0,
                'shipping_name' => "",
                'shipping_fee' => 0,
                'pay_id' => $this->data['pay_id'],
                'pay_name' => PaymentEnums::PAYMENT_TYPE_MAP[$this->data['pay_id']],
                'surplus' => 0,
                'discount' => $goods_list['discount'],
                'goods_amount' => $goods_list['goods_total'],
                'vip_goods_amount' => $goods_list['vip_goods_total'],
                'order_amount' => $order_amount,
                'referer' => $this->referer(),
                'o_status' => code::NS_WAIT_P,
                'r_status' => 0,
                'p_status' => 0,
                'd_status' => 0,
                'add_time' => time(),
                'msg_to_shop' => isset($this->data['msg_to_shop']) ? $this->data['msg_to_shop'] : '',
                'from_mobile' => 1,
                'label' => isset($this->data['label']) ? $this->data['label'] : '',
                'from_where' => $this->referer(),
            ];

            $order_info = array_merge($order_info, $this->bonus);

            /*-是定金的时候 传递 赋值下 order_amount 减少代码量-*/
            $pay_money = $order_amount;

            if ($order_amount == 0) {
                $finish = true;
            }

            if ($finish == true) {
                $order_info['o_status'] = code::NS_WAIT_D;
                $order_info['p_status'] = code::PS_PAYED;
                $order_info['surplus'] = $order_amount;
                $order_info['order_amount'] = 0;
                $order_info['pay_time'] = time();
            }


            $new_order_id = $this->order->name('order_info')->insertGetId($order_info);

            /*-插入商品和赠品到订单商品表-*/
            $this->payment->insert_order_goods($goods_list, $new_order_id, $this->bonus['bonus'], $this->data['once'], $order_info['o_status']);

            /***ck 积分余额变动明细 ***** */
            if ($finish === true) {
                AccountService::service()
                    ->setChangeType(AccountLogEnums::CHANGE_TYPE_USER_CONSUME)
                    ->setChangeDesc(sprintf(AccountLogEnums::USER_CONSUME_MESSAGE, $order_sn, $order_amount))
                    ->setUserId($this->data['user_id'])
                    ->setUserMoney(-$order_amount)
                    ->change();
                //成为会员  上下级关系在队列中设置

                if (!$this->is_vip) {
                    VipUserInfoService::service()->beVip($goods_list['vip_goods_info'], $this->data['user_id']);
                }
                DevelopmentStatusService::service()->initLeadStatus($this->data['user_id'], $new_order_id);

            } else {
                /*  记录在线支付订单操作记录  */
//                $this->payment->insert_pay_log($new_order_id, $pay_money, code::PAY_ORDER);

                $arg = [
                    'form_id' => $form_id,
                    'open_id' => $this->data['openid']
                ];
                $paymentFactory = PaymentFactory::getService($this->data['pay_id']);
                $sign = $paymentFactory->awake($order_sn, $pay_money, $arg);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }

        if ($finish == true) {
            redis()->lPush(RedisKeyEnums::DISTRIBUTION_QUEUE, $new_order_id);
            $tmp = [
                'order_id' => $new_order_id,
                'order_amout' => $order_amount
            ];
            return $this->respondWithArray($tmp, '支付成功');
        }

        return $this->respondWithArray(['sign' => $sign, 'order_id' => $new_order_id, 'order_amount' => $order_amount]);
    }

    private function proxy($order_sn, $total_price, $pay_id, $openid = '', $is_dingjin = 0, $attach = null)
    {

        $total_price = round($total_price, 2);

        switch ($pay_id) {
            case static::alipay_id:
                return $this->alipayApp($order_sn, $total_price, $is_dingjin, $attach);
            case static::alipay_h5_id:
                return $this->alipay_H5($order_sn, $total_price, $is_dingjin, $attach);
            case static::wxpay_id:
                return $this->wxpay($order_sn, $total_price, $openid, $is_dingjin, $attach);
            case static::wxpay_h5_id:
                return $this->wxpay($order_sn, $total_price, $openid, $is_dingjin, $attach, true);
        }
    }

    private function alipayApp($order_sn, $total_amout, $is_dingjin, $attach)
    {

        $alipay = config('alipay.');

        $gateway = Omnipay::create('Alipay_AopApp');
        $gateway->setSignType('RSA'); //RSA/RSA2
        $gateway->setAppId($alipay['appId']);
        $gateway->setPrivateKey($alipay['rsaPrivateKey']);
        $gateway->setAlipayPublicKey($alipay['alipayrsaPublicKey']);

        if ($is_dingjin) {
            $gateway->setNotifyUrl($alipay['NotifyUrldj']);
        } else {
            $gateway->setNotifyUrl($alipay['NotifyUrl']);
        }

        $request = $gateway->purchase();

        $request->setBizContent([
            'subject' => $order_sn,
            'body' => json_encode($attach),
            'out_trade_no' => $order_sn,
            'total_amount' => $total_amout,
            'product_code' => 'QUICK_MSECURITY_PAY',
        ]);

        $response = $request->send();

        if ($response->isSuccessful()) {
            return $response->getOrderString();
        } else {
            abort(400, '网络异常');
        }
    }

    private function alipay_H5($order_sn, $total_amout, $is_dingjin, $attach)
    {

        $alipay = config('alipay.');

        $gateway = Omnipay::create('Alipay_AopWap');
        $gateway->setSignType('RSA'); //RSA/RSA2
        $gateway->setAppId($alipay['appId']);
        $gateway->setPrivateKey($alipay['rsaPrivateKey']);
        $gateway->setAlipayPublicKey($alipay['alipayrsaPublicKey']);
        $gateway->setReturnUrl('https://www.baidu.com/crowd_funding/#/paySuccess');
        if ($is_dingjin) {
            $gateway->setNotifyUrl($alipay['NotifyUrldj']);
        } else {
            $gateway->setNotifyUrl($alipay['NotifyUrl']);
        }

        $request = $gateway->purchase();

        $request->setBizContent([
            'subject' => $order_sn,
            'body' => json_encode($attach),
            'out_trade_no' => $order_sn,
            'total_amount' => $total_amout,
            'product_code' => 'QUICK_MSECURITY_PAY',
        ]);

        $response = $request->send();
        if ($response->isSuccessful()) {
            return ['redirect_url' => $response->getRedirectUrl()];
        } else {
            abort(400, '网络异常');
        }
    }

    private function wxpay($order_sn, $total_amout, $openid, $is_dingjin, $attach, $h5 = false)
    {

        if (in_array($this->source, code::source)) {
            if ($h5 == true) {
                return $this->WechatPay_H5($order_sn, $total_amout, $is_dingjin, $attach);
            } else {
                return $this->WechatPay_Js($order_sn, $total_amout, $openid, $is_dingjin, $attach);
            }
        } else {
            return $this->WechatPay_App($order_sn, $total_amout, $is_dingjin, $attach);
        }
    }

    public function WechatPay_App($order_sn, $total_amout, $is_dingjin, $attach)
    {

        $wxpay = config('wechat.wxpay');
        $gateway = Omnipay::create('WechatPay_App');
        $gateway->setAppId($wxpay['appId']);
        $gateway->setMchId($wxpay['mch_id']);
        $gateway->setApiKey($wxpay['mch_secret']);

        if ($is_dingjin) {
            $gateway->setNotifyUrl($wxpay['NotifyUrldj']);
        } else {
            $gateway->setNotifyUrl($wxpay['NotifyUrl']);
        }

        $order = [
            'body' => $order_sn,
            'out_trade_no' => $order_sn,
            'total_fee' => $total_amout * 100, ////微信支付的单位为分 0.01元
            'spbill_create_ip' => $this->request->ip(),
            'attach' => json_encode($attach)
        ];

        $request = $gateway->purchase($order);
        $response = $request->send();

        if ($response->isSuccessful()) {
            $res = $response->getAppOrderData();
//             $res['timeStamp'] = (int)($res['timeStamp']);
            return $res;
        } else {
            abort(400, '网络异常');
        }
    }


    public function WechatPay_Js($order_sn, $total_amout, $openid, $is_dingjin, $attach)
    {

        $gateway = Omnipay::create('WechatPay_Js');

        if ($this->mendian) {
            $config = config('wechat.wxpay_md');
            $appId = $config['md_Id'];

        } else {
            $config = config('wechat.wxpay');
            if ($this->source == 'h5') {
                $appId = config('wechat.easywechat_config')['app_id'];
                $openid = Db::name('openid')->where('user_id', $GLOBALS['user_id'])->where('platform', 'official_accounts')->value('openid');
            } else {
                $config = config('wechat.wxpay');
                $appId = $config['xcx_Id'];
            }
        }

        $gateway->setAppId($appId);
        $gateway->setMchId($config['mch_id']);
        $gateway->setApiKey($config['mch_secret']);

        if ($is_dingjin) {
            $gateway->setNotifyUrl($config['NotifyUrldj']);
        } else {
            $gateway->setNotifyUrl($config['NotifyUrl']);
        }


        $order = [
            'body' => $order_sn,
            'out_trade_no' => $order_sn,
            'total_fee' => $total_amout * 100, ////微信支付的单位为分 0.01元
            'spbill_create_ip' => $this->request->ip(),
            'open_id' => $openid,
            'attach' => json_encode($attach)
        ];

        $request = $gateway->purchase($order);
        $response = $request->send();
//        $response->getData(); //For debug
        if ($response->isSuccessful()) {
            return $response->getJsOrderData();
        } else {
            abort(400, '网络异常');
        }
    }

    public function WechatPay_H5($order_sn, $total_amout, $is_dingjin, $attach)
    {
        /* @var BaseAbstractGateway $gateway */
        $gateway = Omnipay::create('WechatPay_Mweb');

        $config = config('wechat.wxpay');
        $appId = $config['appId'];

        $gateway->setAppId($appId);
        $gateway->setMchId($config['mch_id']);
        $gateway->setApiKey($config['mch_secret']);

        $gateway->setNotifyUrl($config['NotifyUrl']);

        $order = [
            'body' => $order_sn,
            'out_trade_no' => $order_sn,
            'total_fee' => $total_amout * 100, ////微信支付的单位为分 0.01元
            'spbill_create_ip' => $this->request->ip(),
            'attach' => json_encode($attach)
        ];

        $request = $gateway->purchase($order);
        $response = $request->send();
        if ($response->isSuccessful()) {
            return ['mweb_url' => $response->getMwebUrl()];
        } else {
            abort(400, '网络异常');
        }
    }
}