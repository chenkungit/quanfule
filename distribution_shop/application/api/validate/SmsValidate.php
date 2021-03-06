<?php

namespace app\api\validate;

use think\Validate;

class SmsValidate extends Validate
{
    protected $rule = [
        'type' => 'require|between:1,7',
        'mobile'=>'require|regex:/^1[0-9]{1}\d{9}$/|check_mobile',
        'device_id' => 'require',
        'captcha' => 'require'
    ];

    protected $message = [
        'mobile.require' => '请输入手机号码',
        'mobile.regex' => '请填写正确的手机号码',
        'type.require' => '请输入发送类型',
        'type.between' => '请输入正确的发送类型',
        'captcha' => '请输入验证码'
    ];
    protected $scene = [
        'send' => ['type', 'mobile'],
        'captcha' => ['device_id'],
        'send_v2' => ['type', 'mobile', 'captcha', 'device_id']
    ];

    protected function check_mobile($value, $rule, $data)
    {

        $arr = $this->findUser($data['mobile']);

        if ($data['type'] == 2 && $arr) {
            return '您已经注册过了';
        }

        if ($data['type'] == 4 && $arr) {
            return '该手机已经被绑定过了';
        }

        if (($data['type'] == 1 || $data['type'] == 3) && !$arr) {
            return '该账号不存在';
        }

        return true;
    }


    private function findUser($mobile)
    {
        return db('users')->field('user_id,user_name,password,ec_salt,visit_count')->where(['mobile|user_name' => $mobile, 'is_delete' => 0])->find();
    }

}