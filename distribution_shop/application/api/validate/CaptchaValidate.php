<?php
namespace app\api\validate;

use think\Validate;

class CaptchaValidate extends BaseValidate
{
    protected $rule = [
        'device_id' => 'require',
    ];

    protected $scene = [
        'create' => ['device_id'],
    ];




}