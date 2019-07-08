<?php

namespace app\common\Entity;

use app\common\model\BaseModel;

/**
 * 伞下关系表
 *
 * @property int $user_id
 * @property  int $parent_id
 */
class UmRelation extends BaseModel
{
    protected $table = 'ecs_um_relation';

    public function getInfoByParentId(int $parentId, string $fields = '*')
    {
        return static::getInfo('parent_id', $parentId, $fields);
    }

    public static function getInfoByUserId(int $userId, string $fields = '*')
    {
        return static::getInfo('user_id', $userId, $fields);
    }

    public static function getCollectionByUserId(int $userId, string $fields = '*')
    {
        return static::field($fields)->where('user_id', $userId)->order('level asc')->select()->toArray();
    }
}