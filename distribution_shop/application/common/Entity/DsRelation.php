<?php

namespace app\common\Entity;


use app\common\model\BaseModel;

/**
 * 分销上下级关系表
 * @property int $user_id
 * @property  int $parent_id
 */
class DsRelation extends BaseModel
{
    protected $table = 'ecs_ds_relation';


    public static function getCollectionByUserId(int $userId, $limit = 2, string $fields = 'id')
    {
        return static::field($fields)->where('user_id', $userId)->limit($limit)->select()->toArray();
    }


}