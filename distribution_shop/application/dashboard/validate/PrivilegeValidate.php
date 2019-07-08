<?php

namespace app\dashboard\validate;


use think\Validate;

class PrivilegeValidate extends Validate
{
    protected $rule = [
        'id'=>'require',
        'user_id'=>'require',
        'user_name'=>'require|is_exist',
        'password'=>'require|min:6',
        'repassword'=>'require|min:6|confirm:password',
    ];

    protected $message = [
        'id.require' => '请输入id',
        'id.not_in'=>'admin用户不得删除',
        'user_id.not_in'=>'不能操作admin用户',
        'password.require'=>'请输入密码',
        'repassword.require'=>'请再输入一次密码',
        'repassword.confirm'=>'两次密码不一致',
    ];

    protected $scene = [
        'add'=>['user_name','passowrd','repassword'],
        'allot'=>['user_id'],
        'delete'=>['id'],
        'repassword'=>['user_id','password','repassword']
    ];

    function is_exist($user_name){

        $res  = db('admin_user')->where('user_name',$user_name)->find();
        if($res){

            return '该用户已存在';
        }
        return true;
    }
}