<?php

namespace app\common\Entity;


use app\common\model\BaseModel;


/**
 *
 * @property int $id
 * @property  int $vs_id
 * @property int $user_id
 * @property string $vip_code
 */
class VipUserInfo extends BaseModel
{
    protected $table = 'ecs_vip_user_info';


    public static function getInfoByUserId(int $user_id, $fields = 'vs_id,vip_code')
    {
        return static::field($fields)
            ->where('user_id', $user_id)
            ->find();
    }

    public static function getCollectionByUserIds(array $user_ids, $fields = 'user_id,vs_id,vip_code,vs.name as vip_setting_name')
    {
        return static::field($fields)->alias('vui')->leftJoin('ecs_vip_setting vs', 'vui.vs_id = vs.id')
            ->whereIn('user_id', $user_ids)
            ->select()->toArray();
    }

    public static function getExistByVipCode($vipCode)
    {
        return static::where('vip_code',$vipCode)->count();
    }
}