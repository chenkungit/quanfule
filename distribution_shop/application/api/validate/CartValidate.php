<?php
namespace app\api\validate;

use think\Validate;

class CartValidate extends Validate
{
    protected $rule = [
        'rec_id'=>'require',
        'gshp_id'=>'require',
        'number'=>'require|integer|egt:1',
        'attr_id'=>'array',
        'once'=>'between:0,1',
        'is_dingjin'=>'between:0,1',
    ];

    protected $message = [
        'rec_id.require' => '请输入rec_id',
        'gshp_id.require' => '请输入gshp_id',
        'number.require' => '请输入number',
        'number.integer'=>'number为int',
        'number.egt'=>'数量必须大于1',
        'attr_id'   =>'attr_id必须是数组',
    ];
    protected $scene = [
        'delete'  =>  ['rec_id'],
        'add'=>['gshp_id','number','attr_id']
    ];




}