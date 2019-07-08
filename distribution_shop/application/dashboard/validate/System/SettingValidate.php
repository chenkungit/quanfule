<?php

namespace app\dashboard\validate\System;

use app\api\validate\BaseValidate;

class SettingValidate extends BaseValidate
{

    protected $rule = [
        'value' => 'require',
    ];


    protected $scene = [
        'update' => ['value'],
    ];
}