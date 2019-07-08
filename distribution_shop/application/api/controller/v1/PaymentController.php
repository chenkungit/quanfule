<?php

namespace app\api\controller\v1;

use app\common\controller\ApiController;
use app\common\model\Order;
use Omnipay\Omnipay;
use think\Db;
use think\Request;
use code;
use app\common\model\Payment;
use app\common\model\Users;

class PaymentController extends ApiController
{

    protected $order;
    protected $payment;

    const alipay_id = 4; //支付宝支付
    const alipay_h5_id = 5;
    const wxpay_id = 6;  //微信支付
    const wxpay_h5_id = 7;
    const balance = 1;   //余额支付
    const card_pay = 8;  //会员卡支付

    protected $mendian;
    /*发票 结构体 默认属性*/
    protected $invoice = [
        'user_invoice_id' => 0,
    ];

    /*-红包 结构体-*/
    protected $bonus = [
        'bonus_id' => 0,
        'bonus' => 0,
        'bonus_ship_id' => 0,
        'bonus_ship' => 0,
    ];

    public function __construct(Request $request, Order $order, Payment $payment)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
        $this->order = $order;
        $this->payment = $payment;
    }


    public function financing(Users $users)
    {

        $validate = $this->validate($this->data, 'app\api\validate\PaymentValidate.order_buy');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
        $this->submit_lock(get_class(), 5);
        $openid = isset($this->data['openid']) ? $this->data['openid'] : '';
        $form_id = isset($this->data['form_id']) ? $this->data['form_id'] : null;
        $res = $this->order->name('order_info')->where([['order_id', '=', $this->data['order_id']], ['user_id', '=', $GLOBALS['user_id']], ['o_status', 'in', [code::NS_WAIT_P, code::NS_DINGJIN_P]], ['is_delete', '=', 0]])->find();
        if (!$res) {
            return json_error('', '订单状态错误');
        }
        $payment_info = Db::name('payment')->where([['pay_id', '=', $this->data['pay_id']], ['enabled', '=', 1]])->find();
        if (!$payment_info) {
            return json_error('', '不存在该支付方式', self::INVALID_PARAMETER);
        }

        /*-不同场景下单的解决方案-*/
        $order_sn = $res['order_sn'];

        $pay_money = $res['order_amount'];
        $is_dingjin = 0;


        $update['order_sn'] = $order_sn;
        $update['add_time'] = time();
        /*-如果切换了支付方式 更新一下字段-*/
        if ($this->data['pay_id'] != $res['pay_id']) {
            $update['pay_id'] = $payment_info['pay_id'];
            $update['pay_name'] = $payment_info['pay_name'];
        }

        $this->order->name('order_info')->where('order_id', $res['order_id'])->update($update);
        $attach = [
            'form_id' => $form_id,
            'open_id' => $openid
        ];

        $sign = $this->proxy($order_sn, $pay_money, $this->data['pay_id'], $openid, $is_dingjin, $attach);
        return $this->respondWithArray(['sign' => $sign]);
        // return $this->respondWithArray($new_order_id);
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
        $gateway->setReturnUrl("https://www.baidu.com/crowd_funding/#/paySuccess");
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
            $config = config('wechat.easywechat_config');
            if ($this->source == 'h5') {
                $appId = $config['app_id'];
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

        $gateway = Omnipay::create('WechatPay_Mweb');

        $config = config('wechat.wxpay');
        $appId = $config['appId'];


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
            'attach' => json_encode($attach)
        ];


        $request = $gateway->purchase($order);
        $response = $request->send();
//        $response->getData(); //For debug
        if ($response->isSuccessful()) {
            return ['mweb_url' => $response->getMwebUrl()];
        } else {
            abort(400, '网络异常');
        }

    }
}