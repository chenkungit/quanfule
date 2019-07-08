<?php

namespace app\api\validate\Member;

use app\api\validate\BaseValidate;

class CardValidate extends BaseValidate
{
    protected $rule = [
        'type' => 'require|between:1,2',
        'alipay_account' => 'requireIf:type,1',
        'bank_name' => 'requireIf:type,2',
        'bank_number' => 'requireIf:type,2',
    ];
    protected $message = [
        'type.require' => '请输入类型',
        'alipay_account.requireIf' => '请输入支付宝账号',
        'bank_name.requireIf' => '请输入银行名称',
        'bank_number.requireIf' => '请输入银行账号',
    ];

    protected $scene = [
        'create' => ['type', 'alipay_account', 'bank_name', 'bank_number'],
        'update' => ['type', 'alipay_account', 'bank_name', 'bank_number'],
    ];
}