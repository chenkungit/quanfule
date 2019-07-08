<?php

namespace app\api\validate;


use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'nick' => 'max:25',
        'birthday' => 'dateFormat:Y-m-d',
        'sex' => 'in:1,2',
        'card' => 'require',
        'avatar' => 'file',
        'password' => 'require|checkCard',
        'type_id' => 'require|number',
        'mobile' => 'require',
        'sms_code' => 'require',
        'oldpassword' => 'require|length:6,20',
        'newpassword' => 'require|different:oldpassword',
        're_newpassword' => 'require|confirm:newpassword'
    ];

    protected $message = [
        'type,require' => '请输入优惠卷id',
        'oldpassword.reuqire' => '请输入当前密码',
        'newpassword.require' => '请输入新密码',
        'newpassword.different' => '新旧密码必须不一致',
        're_newpassword.require' => '请再输一次新密码',
        're_newpassword.confirm' => '两次密码不一致',
        'sex.in' => '请选择性别',
    ];
    protected $scene = [
        'info_edit' => ['nick', 'birthday', 'sex'],
        'recharge' => ['card', 'password'],
        'receive_bonus' => ['type_id'],
        'binding' => ['mobile', 'sms_code'],
        'modify_password' => ['oldpassword', 'newpassword', 're_newpassword'],
    ];

    protected function checkCard($value, $rule, $data)
    {
        $password = base64_encode($data['password']);
        $res = db('gcard')->field('id,password,money,is_used,valid_time,able')->where(['card' => $data['card']])->find();
        if (empty($res)) {
            return '该卡号不存在';
        }
        if ($res['password'] != $password) {
            return '密码错误';
        }
        if ($res['is_used'] == 1) {
            return '该礼品卡已被使用过，不可重复使用';
        }
        if ($res['valid_time'] < time()) {
            return '该礼品卡已过有效期';
        }
        if ($res['able'] == 0) {
            return '该礼品卡尚未激活';
        }
        return ['money' => $res['money']];
    }
}