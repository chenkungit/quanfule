<?php
/**
 * User: cpj
 * Date: 2019/6/17
 */

namespace app\api\validate\Member;


use app\api\validate\BaseValidate;

class QrCodeValidate extends BaseValidate
{
    protected $rule = [
        'encode' => 'require',

    ];
    protected $message = [
        'encode.require' => '解析失败',
    ];

    protected $scene = [
        'relate' => ['encode'],
    ];
}