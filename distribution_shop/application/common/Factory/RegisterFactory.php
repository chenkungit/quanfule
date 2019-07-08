<?php
/**
 * User: cpj
 * Date: 2019/6/13
 */

namespace app\common\Factory;


use app\common\Enums\RegisterEnums;

class RegisterFactory
{
    public static function getService(int $register_type)
    {
        switch ($register_type) {
            case RegisterEnums::PASSWORD:

                break;
        }
    }
}