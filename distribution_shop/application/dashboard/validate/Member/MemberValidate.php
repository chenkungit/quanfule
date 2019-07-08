<?php
/**
 * User: cpj
 * Date: 2019/6/18
 */

namespace app\dashboard\validate\Member;

use app\api\validate\BaseValidate;

class MemberValidate extends BaseValidate
{
    protected $rule = [
        'user_id' => 'require',
        'money' => 'require'
    ];

    protected $message = [
        'money.require' => '请输入转入积分'
    ];

    protected $scene = [
        'sendPoint' => ['user_id', 'money'],
        'consumeList' => ['user_id']
    ];
}