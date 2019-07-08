<?php
namespace app\dashboard\validate;

use think\Validate;

class CategoryValidate extends Validate
{
    protected $rule = [
        'type'=>'require|in:1,3',
        'id'=> 'require|number',
        'cat_name'=>'require',
        'parent_id'=>'require|number',
        'sort_order'=>'require',
        'is_show'=>'require|in:0,1',
        'cat_id'=>'require|number'
    ];

    protected $message = [
        'id.require' => '请输入id',
        'cat_name.require'=>'请输入分类名称',
        'parent_id'=>'请选择顶级分类',
    ];
    protected $scene = [
        'add' =>['cat_name','parent_id'],
        'edit'=>['cat_id','parent_id'],
        'delete'=>['type','id']
    ];


}