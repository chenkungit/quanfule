<?php

namespace app\common\Utils\Payment;

use app\api\exception\IllegalException;
use app\common\Mapping\PaymentInterface;
use Omnipay\Omnipay;
use Omnipay\WechatPay\BaseAbstractGateway;

class PaymentWechatH5 implements PaymentInterface
{

    public function awake($orderSn, $totalAmount, $arg)
    {
        $attach = [];

        /* @var BaseAbstractGateway $gateway */
        $gateway = Omnipay::create('WechatPay_Mweb');

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
            'attach' => json_encode($attach)
        ];

        $request = $gateway->purchase($order);
        $response = $request->send();

        dd($response->getData());
        if ($response->isSuccessful()) {
            return ['mweb_url' => $response->getMwebUrl()];
        } else {
            throw new IllegalException('系统异常');
        }
    }
}