<?php

namespace app\api\validate;


use think\Validate;

class PaymentValidate extends Validate
{
    protected $rule = [
        'order_id' => 'require',
        'address_id' => 'require',
        'pay_id' => 'in:4,5,6,7,8',
//        'shipping_id'=>'require'
    ];

    protected $message = [
        'order_id.require' => '请输入订单id',
        'address_id.require' => '请选择地址',
        'pay_id.in' => '支付方式错误',
//        'shipping_id.require'=>'请选择快递方式'
    ];
    protected $scene = [
        'done' => ['address_id', 'pay_id'],
        'order_buy' => ['order_id', 'pay_id'],
    ];


}