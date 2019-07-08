<?php

namespace app\common\Enums;

class RedisKeyEnums
{
    const PREFIX = 'shop:';

    const ACCESS_TOKEN = self::PREFIX . 'access_token:%s';

//    const UNIQUE_TOKEN = self::PREFIX.'unique_token_'

    const TOKEN_INDEX = self::PREFIX . 'token_index:%d';//token索引 维护token关系和数量

    const SUBMIT_LOCK = self::PREFIX . 'submit_lock:%s:%d';

    const SMS_CONTROL_HASH = self::PREFIX . 'sms_control_hash:%d:%s';

    const CAPTCHA = self::PREFIX . 'captcha:%s';

    const CACHE_INDEX = self::PREFIX . 'cache_index:%d:%s:%d:%d:%d:%d';

    const CACHE_CATEGORY = self::PREFIX . 'parent_categories_tree:%d:%d';
    //分销脚本消费队列
    const DISTRIBUTION_QUEUE = self::PREFIX . 'distribution_queue';

    const DISTRIBUTION_ERROR_QUEUE = self::PREFIX . 'distribution_error_queue';

    const SYSTEM_SETTING_HASH = self::PREFIX . 'system_setting';

    const MONTH_TTL = 2592000; /*-一个月的秒数*/

    const TWO_HOURS_TTL = 3600;


}