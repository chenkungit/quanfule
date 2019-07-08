<?php

namespace app\common\Enums;


class PaymentEnums
{
    const PAYMENT_TYPE_SURPLUS = 1;

    const PAYMENT_TYPE_WECHAT_JS = 6;

    const PAYMENT_TYPE_WECHAT_H5 = 7;

    const PAYMENT_TYPE_MAP = [
        self::PAYMENT_TYPE_SURPLUS => '积分支付',
        self::PAYMENT_TYPE_WECHAT_JS => '微信js支付',
        self::PAYMENT_TYPE_WECHAT_H5 => '微信h5支付'
    ];
}