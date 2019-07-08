<?php
/**
 * Created by PhpStorm.
 * User: losingbattle
 * Date: 2019/6/14
 * Time: 21:28
 */

namespace app\common\Utils;


class Request
{

    public static function getLimit()
    {
        return Request()->param('limit', 15);
    }


    public static function getPage()
    {
        return Request()->param('page', 1);
    }
}