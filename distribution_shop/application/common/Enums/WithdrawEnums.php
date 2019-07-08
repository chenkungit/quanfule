<?php
/**
 * Created by PhpStorm.
 * User: losingbattle
 * Date: 2019/6/19
 * Time: 22:24
 */

namespace app\common\Enums;


class WithdrawEnums
{
    const STATUS_ONGOING = 0;

    const STATUS_FINISH = 1;

    const STATUS_REJECT = 2;

    const STATUS_MAP = [
        self::STATUS_ONGOING => '审核中',
        self::STATUS_FINISH => '提现完成',
        self::STATUS_REJECT => '拒绝'
    ];
}