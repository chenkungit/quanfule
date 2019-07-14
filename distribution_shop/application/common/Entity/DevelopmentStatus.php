<?php

namespace app\common\Entity;

use app\common\model\BaseModel;

/**
 * 是否可领终生成就奖
 * @property int $id
 * @property  int $user_id
 * @property int $order_id
 * @property string $expire_date
 * @property string $expire_date_month;
 */
class DevelopmentStatus extends BaseModel
{

    protected $table = 'ecs_development_status';

    public static function getUnexpiredStatus(int $user_id)
    {
        static::where('expire_date', '>=', date('Y-m-d'))->where('user_id', $user_id)->find();
    }
}