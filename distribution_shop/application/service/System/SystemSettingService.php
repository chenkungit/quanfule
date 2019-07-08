<?php

namespace app\service\System;

use app\common\Entity\SystemSetting;
use app\common\Enums\RedisKeyEnums;
use app\common\Enums\SystemSettingEnums;
use app\service\BaseService;

class SystemSettingService extends BaseService
{
    public function getPageList()
    {
        return SystemSetting::getPageList();
    }

    public function create()
    {


    }

    public function update(array $set)
    {
        $systemSetting = SystemSetting::getInfoById($set['id']);
        if (!$systemSetting) {
            throw new \Exception('不存在');
        }
        redis()->hSet(RedisKeyEnums::SYSTEM_SETTING_HASH, $systemSetting->key, $set['value']);
        $systemSetting->value = $set['value'];
        $systemSetting->save();
    }


    public function getWechatPayStatus()
    {
        return $this->getSystemFromRedis(SystemSettingEnums::WECHAT_PAY);
    }

    public function getTransferServiceChargeRate()
    {
        return $this->getSystemFromRedis(SystemSettingEnums::TRANSFER_SERVICE_CHARGE_RATE);
    }


    public function getWithdrawServiceChargeRate()
    {
        return $this->getSystemFromRedis(SystemSettingEnums::WITHDRAW_SERVICE_CHARGE_RATE);
    }

    public function getThankfulRate()
    {
        return $this->getSystemFromRedis(SystemSettingEnums::THANKFUL_RATE);
    }

    public function getConvertServiceChargeRate()
    {
        return $this->getSystemFromRedis(SystemSettingEnums::CONVERT_SERVICE_CHARGE_RATE);
    }

    public function getSystemFromRedis(string $key)
    {
        $res = redis()->hGet(RedisKeyEnums::SYSTEM_SETTING_HASH, $key);

        if ($res === false) {
            $data = SystemSetting::field('key,value')->column('value', 'key');
            redis()->hMset(RedisKeyEnums::SYSTEM_SETTING_HASH, $data);
            return $this->getSystemFromRedis($key);
        }
        return $res;
    }
}