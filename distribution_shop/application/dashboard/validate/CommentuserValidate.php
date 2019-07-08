<?php
namespace app\dashboard\validate;

use think\Validate;

class CommentuserValidate extends Validate
{
    protected $rule = [
        'comment_id'=> 'require|number',
        'goodstype_id'=> 'require|number',
        'attr_name'=>'require',
    ];

    protected $message = [
        'comment_id.require' => '请输入comment_id',
        'goodstype_id.require' => '请选择商品类型',
        'attr_name.require'=>'请输入属性名称',
    ];

    protected $scene = [
        'info' =>['comment_id'],
        'edit'=>['comment_id'],
        'delete'=>['comment_id'],

    ];
}