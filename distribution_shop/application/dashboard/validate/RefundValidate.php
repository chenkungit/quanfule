<?php
namespace app\dashboard\validate;


use think\Validate;

class RefundValidate extends Validate
{
    protected $rule = [
        'refund_id'=> 'require|number',
        'action_note'=>'require'
    ];

    protected $message = [
        'refund_id.require' => '请输入refund_id',
        'action_note.require'=>'请输入备注',
    ];

    protected $scene = [
        'back'=>['refund_id','action_note'],
    ];
}