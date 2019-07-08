<?php

namespace app\common\Factory;


use app\common\Enums\SmsEnums;
use app\common\Utils\Sms\DecentSms;

class SmsFactory
{
    public static function getService(string $type)
    {
        switch ($type) {
            case SmsEnums::DECENT:
                return new DecentSms();
                break;
        }
    }
}