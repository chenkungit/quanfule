<?php
namespace app\dashboard\validate;

use think\Validate;

class NavValidate extends Validate
{
    protected $rule = [
        'n_id'=> 'require|number',
        'n_name'=>'require',
        'redirect_type'=>'require|number',
        'redirect_id'=>'require',
        'sort'=>'require|number',
        'enabled'=>'require|between:0,1',
    ];

    protected $message = [
        'id.require' => '请输入id',
        'n_name.require'=>'请输入导航栏名称',
        'redirect_type.require'=>'跳转类型不能为空',
        'redirect_id'=>'跳转id不能为空',
        'sort'=>'请输入排序'
    ];

    protected $scene = [
        'info'=>['id'],
        'selected'=>['id'],
        'add' =>['n_name','redirect_type','redirect_id','sort'],
        'edit'=>['id','n_name','redirect_type','redirect_id','sort'],
        'delete'=>['id']
    ];


}