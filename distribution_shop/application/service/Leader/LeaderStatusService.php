<?php

namespace app\service\Leader;

use app\common\Entity\LeaderStatus;
use app\service\BaseService;

class LeaderStatusService extends BaseService
{

    //是否有领导奖状态 每月购买一次商品
    public function getUnexpiredStatus(int $user_id)
    {
        if (LeaderStatus::getUnexpiredStatus($user_id)) {
            return true;
        }
        return false;
    }

    public function initLeadStatus($user_id, $order_id, $goods_id)
    {

        if ($this->getUnexpiredStatus($user_id)) {
            return;
        }
        $leaderStatus = new LeaderStatus();

        $leaderStatus->user_id = $user_id;
        $leaderStatus->order_id = $order_id;
        $leaderStatus->goods_id = $goods_id;
        $leaderStatus->expire_date = getTheMonthLastDay();
        $leaderStatus->expire_date_month = date('Y-m');

        $leaderStatus->save();
    }
}