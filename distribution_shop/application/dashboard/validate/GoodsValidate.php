<?php
namespace app\dashboard\validate;


use think\Validate;

class GoodsValidate extends Validate
{
    protected $rule = [
        'id'=> 'require|number',
        'is_sale'=> 'require|number',
        'gsup_id_arr'=>'require',
        'name' => 'require',
        'weight' => 'require',
        'category' => 'require',
        'supplier' => 'require',
        'price' => 'require',
        'vs_id' => 'require',
    ];

    protected $scene = [
        'edit'=>['id'],
        'update_sale'=>['gsup_id_arr','is_sale'],
        'sup_insert' =>['name','weight','category','vs_id','price'],
    ];
}