<?php
/**
 * Created by PhpStorm.
 * User: losingbattle
 * Date: 2019/6/15
 * Time: 10:35
 */

namespace app\common\Factory;


use app\common\Enums\UploadEnums;
use app\common\Utils\LocalUpload;

class UploadFactory
{
    public static function getService(int $type)
    {
        switch ($type) {
            case UploadEnums::LOCAL:
                return new LocalUpload();
                break;
        }
    }
}