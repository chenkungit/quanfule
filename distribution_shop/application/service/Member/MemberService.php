<?php

namespace app\service\Member;

use app\common\Entity\Users;
use app\common\Entity\VipUserInfo;
use app\service\BaseService;
use app\service\Vip\VipUserInfoService;

class MemberService extends BaseService
{
    public function getPageList(array $requestData)
    {
        $data = Users::getPageList($requestData);

        $vipTemp = [];

        foreach ($data['list'] as $item) {
            if ($item['is_vip'] == 1) {
                $vipTemp[] = $item['user_id'];
            }
        }

//        VipUserInfoService::service()->getCollection()
        if ($vipTemp) {
            $vipUserCollection = VipUserInfo::getCollectionByUserIds($vipTemp);
            $vipUserCollection = array_column($vipUserCollection, null, 'user_id');

            foreach ($data['list'] as &$item) {
                if (isset($vipUserCollection[$item['user_id']])) {
                    $item = array_merge($item, $vipUserCollection[$item['user_id']]);
                }
            }
        }

        return $data;
    }

    public function getTreasure(int $userId)
    {
        $res = Users::getInfoByUserId($userId, 'is_vip,user_money,frozen_money,prize_money,frozen_prize_money');
        return $res;
    }

}