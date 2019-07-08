<?php

namespace app\common\Factory;


use app\common\Enums\PaymentEnums;
use app\common\Utils\Payment\PaymentWechatH5;
use app\common\Utils\Payment\PaymentWechatJS;

class PaymentFactory
{
    public static function getService(int $paymentType)
    {
        switch ($paymentType) {
            case PaymentEnums::PAYMENT_TYPE_SURPLUS:
//                return
                break;
            case PaymentEnums::PAYMENT_TYPE_WECHAT_JS:
                return new PaymentWechatJS();
                break;
            case PaymentEnums::PAYMENT_TYPE_WECHAT_H5:
                return new PaymentWechatH5();
                break;
        }
    }
}