<?php

namespace app\service\Vip;


use app\api\exception\ValidateException;
use app\common\Entity\VipSetting;
use app\service\BaseService;

class VipSettingService extends BaseService
{

    public function getPageList(array $requestData)
    {
        return VipSetting::getPageList($requestData);

    }

    public function create(array $data)
    {
        if (VipSetting::getInfoByLevel($data['level'])) {
            throw new ValidateException('已存在该等级的会员');
        }

        VipSetting::create($data);
    }

    public function update(array $set)
    {

        $vipSetting = VipSetting::getInfoById($set['id']);

        if ($vipSetting->level != $set['level']) {
            if (VipSetting::getInfoByLevel($set['level'])) {
                throw new ValidateException('已存在该等级的会员');
            }
        }

        $vipSetting->save($set);
    }
}