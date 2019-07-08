<?php
namespace app\api\validate;

use think\Validate;

class GoodsValidate extends Validate
{
    protected $rule = [
        'gsup_id'=> 'require',
        'number'=>'require',
        'label'=>'require',
        'mobile'=>'require|regex:/^1[0-9]{1}\d{9}$/',
    ];

    protected $message = [
        'gsup_id.require' => '请输入商品gsup_id',
        'number.require'=>'请输入商品数量',
        'mobile.require' => '请输入手机号',
        'mobile.regex'=>'手机格式错误',
    ];
    protected $scene = [
        'price'  =>  ['gsup_id','number'],
        'remind'=>['gsup_id','mobile'],
        'cancle_remind'=>['gsup_id']
    ];





}