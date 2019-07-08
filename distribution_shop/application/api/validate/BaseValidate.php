<?php

namespace app\api\validate;


use app\api\exception\ValidateException;
use think\Validate;

class BaseValidate extends Validate
{
    public function check($data, $rules = [], $scene = '')
    {
        if (!parent::check($data, $rules, $scene)) {
            throw new ValidateException($this->getError());
        }
        return true;
    }
}