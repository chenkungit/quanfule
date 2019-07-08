<?php

namespace app\common\Utils;

use app\common\Enums\RedisKeyEnums;

class Token
{

    public static function getAccessToken(string $string)
    {
        return sprintf(RedisKeyEnums::ACCESS_TOKEN, $string);
    }

    public static function getTokenIndex(int $index)
    {
        return sprintf(RedisKeyEnums::TOKEN_INDEX, $index);
    }
}