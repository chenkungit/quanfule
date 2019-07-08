<?php

namespace app\common\Entity;


use app\common\model\BaseModel;


/**
 * @property int $id
 * @property string $key
 * @property string $value
 */
class SystemSetting extends BaseModel
{
    protected $table = 'ecs_system_setting';


    /* @return SystemSetting */
    public static function getInfoById($id, $fields = '*')
    {
        return static::getInfo('id', $id, $fields);
    }


    public static function getCollection()
    {

    }
}