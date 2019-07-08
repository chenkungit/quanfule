<?php

namespace app\api\validate;

use think\Validate;

class CategoryValidate extends Validate
{
    protected $rule = [
        'cat_id'=>'require',
    ];

    protected $message = [
        'cat_id.require' => '请输入cat_id',
    ];
    protected $scene = [
        'equal_list'  =>  ['cat_id'],
    ];
}