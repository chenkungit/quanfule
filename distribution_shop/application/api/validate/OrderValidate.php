<?php
namespace app\api\validate;

use think\Validate;

class OrderValidate extends Validate
{
    protected $rule = [
        'order_id'=>'require|number',
        'goods_json'=>'require',
        'address_id'=>'require|number',
        'pay_id'=>'require|number',
        'rec_id'=>'require|number',
        'number'=>'require|number',
        'goods_status'=>'require|between:0,1',
        'refund_type'=>'require|between:1,2',
        'reason_type'=>'require|between:1,7',
        'refund_info'=>'require',
        'is_balance'=>'require|between:0,1',
        'order_comment'=>'require'
    ];

    protected $message = [
        'order_id.require' => '请输入订单id',
        'address_id.require' => '请输入地址id',
        'pay_id.require'=> '请输入支付方式id',
        'rec_id.require'=> '请输入退货商品id',
        'number.require'=>'请输入退货数量',
        'goods_status.require'=>'请选择货物状态',
        'goods_status.between'=>'请选择正确的货物状态',
        'refund_type.require'=>'请选择退货类型',
        'refund_type.between'=>'请选择正确的退货类型',
        'reason_type.require'=>'请选择退货原因',
        'reason_type.between'=>'请选择正确的退货原因',
        'refund_info.require'=>'请填写具体原因',
        'is_balance.require'=>'请选择是否使用余额支付',
        'is_balance.between'=>'余额状态错误',
        'order_comment.require'=>'评论信息不能为空'
    ];
    protected $scene = [
        'cancel'  =>  ['order_id'],
        'order_detail'  =>  ['order_id'],
        'refund'  =>  ['order_id','rec_id','goods_status','reason_type','number','refund_type','refund_info'],
        'refund_v2'    =>['order_id','goods_json','goods_status','reason_type','refund_type','refund_info'],
        'cancel_refund'=>['order_id'],
        'order_buy'=>['order_id','pay_id','is_balance'],
        'tracking'=>['order_id'],
        'comment'=>['order_id','order_commentorder_comment']
    ];


}