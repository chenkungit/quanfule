<?php

namespace app\common\Entity;


use think\Db;
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
        return static::field($fields)->where('user_id',$userId)->limit($limit)->select()->toArray();
    }

    public function deleteDefaultRelation($userId)
    {
        return Db::table('ecs_ds_relation')->where(['user_id'=>$userId,'parent_id'=>0,'level'=>0])->delete();
    }

    //递归查询查询所有的上级人员
    public function getUpUser($userid,&$result)
    {
        $temp_ids = Db::name('ds_relation')->where('user_id', $userid)->order('level')->column('parent_id');
        if(count($temp_ids) === 3){
            return $this->getUpUser($temp_ids[2],$temp_ids);
        }else{
            return array_merge($result,$temp_ids);
        }
    }
    //更新业绩
    public function updateAchieveByUser($userids,$achieve){
        Db::startTrans();
        try {
            foreach($userids as $user_id){
                Db::table('ecs_ds_relation')->where(['user_id'=>$user_id,'level'=>1])->update(['current_achieve'=>Db::row('current_achieve+'.$achieve)]);
            }
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }

    }


}