<?php
namespace app\dashboard\validate;

use think\Validate;
class AttributeValidate extends Validate
{
    protected $rule = [
        'goodstype_id'=> 'require|number',
        'attr_name'=>'require',
    ];

    protected $message = [
        'attr_id.require' => '请输入id',
        'goodstype_id.require' => '请选择商品类型',
        'attr_name.require'=>'请输入属性名称',


    ];
    protected $scene = [
        'add' =>['attr_name','goodstype_id'],
        'edit'=>['goodstype_id','attr_name'],

    ];
}