<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2017/12/4
 * Time: 9:34
 */

namespace app\dashboard\validate;

use think\Validate;
class GoodstypeValidate extends Validate
{
    protected $rule = [
        'cat_id'=> 'require|number',
        'cat_name'=>'require|unique:goods_type',
    ];

    protected $message = [
        'cat_id.require' => '请选择其中一条',
        'cat_name.require'=>'请输入分类名称',
        'cat_name.unique'=>'分类名称已存在',

    ];

    protected $scene = [
        'add' =>['cat_name'],
        'edit'=>['cat_id','cat_name'],
        'delete'=>['cat_id']
    ];

}