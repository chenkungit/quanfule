<?php

namespace app\common\Utils\Payment;

use app\api\exception\IllegalException;
use app\common\Mapping\PaymentInterface;
use Omnipay\Omnipay;
use Omnipay\WechatPay\BaseAbstractGateway;

class PaymentWechatJS implements PaymentInterface
{

    public function awake($orderSn, $totalAmount, $arg)
    {
        $attach = [];

        /* @var BaseAbstractGateway $gateway */
        $gateway = Omnipay::create('WechatPay_Js');

        $config = config('wechat.wxpay');

        $appId = $config['appId'];

        $gateway->setAppId($appId);
        $gateway->setMchId($config['mch_id']);
        $gateway->setApiKey($config['mch_secret']);
        $gateway->setNotifyUrl($config['NotifyUrl']);


        $order = [
            'body' => $orderSn,
            'out_trade_no' => $orderSn,
            'total_fee' => $totalAmount * 100, ////微信支付的单位为分 0.01元
            'spbill_create_ip' => request()->ip(),
            'open_id' => $arg['open_id'],
            'attach' => json_encode($attach)
        ];

        $request = $gateway->purchase($order);
        $response = $request->send();
//        dd($response->getData()); //For debug
        if ($response->isSuccessful()) {
            return $response->getJsOrderData();
        } else {
            abort(400, '网络异常');
        }

    }
}