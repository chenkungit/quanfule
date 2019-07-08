<?php
namespace app\dashboard\validate;

use think\Validate;

class HomethemeValidate extends Validate
{
    protected $rule = [
        't_id'=> 'require|number',
        'theme_name'=>'require',
        'redirect_type'=>'require|number',
        'redirect_id'=>'require',
        'children_goods_id'=>'require',
        'sort'=>'require',
        'enabled'=>'require|between:0,1',
    ];

    protected $message = [
        't_id.require' => '请输入id',
        'theme_name.require'=>'请输入主题名称',
        'redirect_type.require'=>'跳转类型不能为空',
        'redirect_id'=>'跳转id不能为空',
        'children_goods_id'=>'请选择商品',
        'sort'=>'请输入排序'
    ];

    protected $scene = [
        'info'=>['id'],
        'add' =>['theme_name','redirect_type','redirect_id','children_goods_id','sort','enabled'],
        'edit'=>['id','theme_name','redirect_type','redirect_id','children_goods_id','sort','enabled'],
        'delete'=>['id']
    ];


}