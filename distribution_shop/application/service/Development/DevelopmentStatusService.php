<?php

namespace app\service\Development;

use app\common\Entity\DevelopmentStatus;
use app\common\Entity\DsRelation;
use app\service\BaseService;

class DevelopmentStatusService extends BaseService
{

    //是否有领导奖状态 每月购买一次商品
    public function getUnexpiredStatus(int $user_id)
    {
        if (DevelopmentStatus::getUnexpiredStatus($user_id)) {
            return true;
        }
        return false;
    }

    public function initLeadStatus($user_id, $order_id)
    {
        //update bu ck 领导状态可以重复，便于计算终身成就奖励
        // if ($this->getUnexpiredStatus($user_id)) {
        //     return;
        // }
        $leaderStatus = new DevelopmentStatus();

        $leaderStatus->user_id = $user_id;
        $leaderStatus->order_id = $order_id;
        $leaderStatus->expire_date = getTheMonthLastDay();
        $leaderStatus->expire_date_month = date('Y-m');

        $leaderStatus->save();
    }
    /**
     * 为所有上级添加业绩（包括本人）
     */
    public function setAchieveToUpUsers($user_id,$vip_goods_total){
        $dsRelation = new DsRelation();
        //获取所有上级人员
        $results =  $dsRelation->getUpUser($user_id,$results);
        //添加自己构建完整链
        array_push($results,$user_id);
        $dsRelation->updateAchieveByUser($results,$vip_goods_total);
    }
}