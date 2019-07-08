<?php

namespace app\service\Development;

use app\common\Entity\DevelopmentStatus;
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

        if ($this->getUnexpiredStatus($user_id)) {
            return;
        }
        $leaderStatus = new DevelopmentStatus();

        $leaderStatus->user_id = $user_id;
        $leaderStatus->order_id = $order_id;
        $leaderStatus->expire_date = getTheMonthLastDay();
        $leaderStatus->expire_date_month = date('Y-m');

        $leaderStatus->save();
    }
}