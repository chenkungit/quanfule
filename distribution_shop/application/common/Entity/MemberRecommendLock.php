<?php

namespace app\common\Entity;


use app\common\model\BaseModel;


/**
 * This is the model class for table "ecs_user_card_info".
 *
 * @property int $user_id
 * @property  int $recommend_user_id
 */
class MemberRecommendLock extends BaseModel
{
    protected $table = 'ecs_member_recommend_lock';


}