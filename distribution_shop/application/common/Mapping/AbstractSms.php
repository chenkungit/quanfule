<?php

namespace app\common\Mapping;


abstract class AbstractSms
{
    abstract function send($phoneNumber, $templateId, $data);
}