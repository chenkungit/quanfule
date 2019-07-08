<?php

namespace app\service\Vip;

use app\common\Entity\DsRelation;
use app\common\Entity\MemberRecommendLock;
use app\common\Enums\AccountLogEnums;
use app\service\BaseService;
use app\service\Member\AccountService;

class RecommendService extends BaseService
{
    public function recommend($userId, $vsId)
    {
        $dsRelation = DsRelation::where('user_id', $userId)->where('level', 1)->find();

        if (!$dsRelation) {
            return;
        }


        $vipUserInfo = VipUserInfoService::service()->getInfo($dsRelation['user_id']);
        //等级不同return
        if ($vipUserInfo['id'] != $vsId) {
            return;
        }

        $memberRecommendLock = new MemberRecommendLock();

        $memberRecommendLock->user_id = $dsRelation['parent_id'];
        $memberRecommendLock->recommend_user_id = $userId;
        $memberRecommendLock->save();

        $recommendLockCount = MemberRecommendLock::where('user_id', $dsRelation['parent_id'])->count();

        //每发展两个同级会员得积分
        if ($recommendLockCount / 2 != 1) {
            return;
        }

        AccountService::service()
            ->setUserId($dsRelation['parent_id'])
            ->setUserMoney($vipUserInfo['recommend_award'])
            ->setChangeType(AccountLogEnums::CHANGE_TYPE_RECOMMEND)
            ->setChangeDesc(sprintf(AccountLogEnums::RECOMMEND_MESSAGE, $vipUserInfo['recommend_award']))
            ->change();
    }
}