<?php

namespace app\api\validate\Member;

use app\api\validate\BaseValidate;

class PointValidate extends BaseValidate
{
    protected $rule = [
        'to_user_id' => 'require',
        'money' => 'require|gt:0',
        'point' => 'require|gt:0',
        'member_card_id' => 'require'
    ];
    protected $message = [
        'to_user_id.require' => '请选择要转入的用户',
        'money.require' => '请输入提现金额',
        'member_card_id' => '请选择提现方式',
        'point.require' => '请输入积分',
        'point.gt' => '转出积分必须大于0',
        'money.gt' => '提现金额必须大于0元'
    ];

    protected $scene = [
        'transfer' => ['to_user_id', 'point'],
        'withdrawApply' => ['money', 'member_card_id'],
        'convert' => ['money']
    ];
}