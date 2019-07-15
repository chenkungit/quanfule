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
        return static::field($fields)->where('user_id',$userId)->where('level','>',0)->limit($limit)->select()->toArray();
    }
    //获取上级人员各自的返现比例
    public static function getCollectionVipInfoByUserId(int $userId, $limit = 3)
    {
        return Db::table('ecs_ds_relation')->alias('r')->field('r.parent_id as parent_id,r.level as level,s.first_reback_rate as first_reback_rate,s.second_reback_rate as second_reback_rate,s.third_reback_rate as third_reback_rate')
                ->leftJoin('ecs_vip_user_info v','v.user_id = r.parent_id')
                ->leftJoin('ecs_vip_setting s','s.id = v.vs_id')
                ->where('r.user_id',$userId)->where('r.level','>',0)->limit($limit)->select(); 
    }

    public function deleteDefaultRelation($userId)
    {
        return Db::table('ecs_ds_relation')->where(['user_id'=>$userId,'parent_id'=>0,'level'=>0])->delete();
    }

    //递归查询查询所有的上级人员
    public function getUpUser($userid,&$result)
    {
        $temp_ids = Db::name('ds_relation')->where('user_id', $userid)->where('level','>',0)->order('level')->column('parent_id');
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
                Db::table('ecs_ds_relation')->where(['user_id'=>$user_id,'level'=>1])->update(['current_achieve'=>Db::raw('current_achieve+'.$achieve)]);
            }
            Db::commit();
        }catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }

    }


}