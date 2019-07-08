<?php
namespace app\dashboard\validate;

use think\Validate;

class AuthValidate extends Validate
{
    protected $rule = [
        'account'=> 'require',
        'password'=> 'require|checkUser',
    ];

    protected $message = [
        'account.require' => '请输入用户名',
        'password.require' => '请输入密码',
    ];
    protected $scene = [
        'login' =>   ['account','password'],
    ];



    protected function checkUser($value,$rule,$data){

        $arr = $this->findUser($data['account']);

        if($arr){
            $md5Password = md5(md5($data['password']).$arr['ec_salt']);
            if($md5Password == $arr['password']){
                return $arr;
            }else{
                return '用户或密码错误';
            }
        }else{
            return '该用户不存在';
        }

    }

    private function findUser($account){
        return db('admin_user')->field('user_id,user_name,password,ec_salt,node_list')->where(['user_name'=>$account])->find();
    }
}