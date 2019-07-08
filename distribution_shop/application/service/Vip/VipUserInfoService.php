<?php

namespace app\service\Vip;

use app\common\Entity\Users;
use app\common\Entity\VipUserInfo;
use app\common\Entity\VipSetting;
use app\common\Utils\Token;
use app\service\BaseService;

class VipUserInfoService extends BaseService
{
    public function getInfo(int $user_id)
    {
        /* @var VipUserInfo $vipInfo */
        $vipInfo = VipUserInfo::getInfoByUserId($user_id);

        if (!$vipInfo) {
            return false;
        }

        $vipSettingInfo = VipSetting::getInfoById($vipInfo->vs_id);

        $vipSettingInfo['vip_code'] = $vipInfo['vip_code'];

        return $vipSettingInfo;
    }

    public function getCollection(array $user_ids)
    {

        $vipUserIds = [];

        $userCollection = Users::getCollectionByUserId($user_ids, 'user_id,is_vip,user_name,nick');

        foreach ($userCollection as $item) {
            if ($item['is_vip'] == 1) {
                $vipUserIds[] = $item['user_id'];
            }
        }

        if ($vipUserIds) {
            $vipUserInfoCollection = VipUserInfo::getCollectionByUserIds($user_ids);
            $vipUserInfoCollection = array_column($vipUserInfoCollection, null, 'user_id');
        }


        if (isset($vipUserInfoCollection)) {
            foreach ($userCollection as &$item) {
                if (isset($vipUserInfoCollection[$item['user_id']])) {
                    $item = array_merge($item, $vipUserInfoCollection[$item['user_id']]);
                }
            }
        }

        return $userCollection;
    }

    //array(2) {
    //  [1271]=>
    //  int(1)
    //  [1698]=>
    //  int(4)
    //}
    public function beVip(array &$vipGoodsInfo, int $user_id)
    {
        if (!$vipGoodsInfo) {
            return;
        }
        $vipUserInfo = new VipUserInfo();


        $vipUserInfo->user_id = $user_id;
        $vipUserInfo->vip_code = $this->getVipCode();

        if (count($vipGoodsInfo) == 1) {
            $vs_id = current(array_values($vipGoodsInfo));
            $vipSettingInfo = VipSetting::getInfoById($vs_id, 'id,discount');
        } else {
            $vipSettingInfo = VipSetting::getMaxLevelInfoByVsIds(array_values($vipGoodsInfo));
        }
        $set['is_vip'] = 1;

        Users::updateByUserId($set, $user_id);

        $set['discount'] = $vipSettingInfo->discount;



        $vipUserInfo->vs_id = $vipSettingInfo->id;
        $vipUserInfo->save();

        RecommendService::service()->recommend($user_id, $vipSettingInfo->id);

        $token = redis()->get(Token::getTokenIndex($user_id));
        if ($token) {
            redis()->hMset(Token::getAccessToken($token), $set);
        }

        unset($vipGoodsInfo[array_search($vipSettingInfo->id, $vipGoodsInfo)]);
    }


    private function getVipCode()
    {
        $random = rand(100000, 999999);
        if (VipUserInfo::getExistByVipCode($random)) {
            return $this->getVipCode();
        }
        return $random;
    }
}