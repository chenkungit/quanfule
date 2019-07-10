<?php
/**
 * User: cpj
 * Date: 2019/6/18
 */

namespace app\common\Enums;


class AccountLogEnums
{
    const CHANGE_TYPE_SYSTEM_TRANSFER = 1;

    const CHANGE_TYPE_USER_CONSUME = 2;

    const CHANGE_TYPE_REBATE_PRIZE = 3;

    const CHANGE_TYPE_APPLY_WITHDRAW = 4;

    const CHANGE_TYPE_WITHDRAW_SUCCESS = 5;

    const CHANGE_TYPE_LEADER_PRIZE = 6;

    const CHANGE_TYPE_ACHIEVEMENT_AWARD = 7;

    const CHANGE_TYPE_USER_OUT = 8;

    const CHANGE_TYPE_USER_IN = 9;

    const CHANGE_TYPE_REFUND = 10;

    const CHANGE_TYPE_CONVERT = 11;

    const CHANGE_TYPE_DEVELOPMENT = 12;

    const CHANGE_TYPE_THANKFUL = 13;

    const CHANGE_TYPE_RECOMMEND = 14;

    const CHANGE_TYPE_OTHER = 99;

//    const CHANGE_TYPE_

    const CHANGE_TYPE_MAP = [
        self::CHANGE_TYPE_SYSTEM_TRANSFER => '系统转账',
        self::CHANGE_TYPE_USER_CONSUME => '用户消费',
        self::CHANGE_TYPE_REBATE_PRIZE => '返佣奖励',
        self::CHANGE_TYPE_APPLY_WITHDRAW => '申请提现',
        self::CHANGE_TYPE_WITHDRAW_SUCCESS => '提现成功',
        self::CHANGE_TYPE_LEADER_PRIZE => '领导奖奖励',
        self::CHANGE_TYPE_ACHIEVEMENT_AWARD => '业绩奖奖励',
        self::CHANGE_TYPE_USER_OUT => '积分转出',
        self::CHANGE_TYPE_USER_IN => '积分转入',
        self::CHANGE_TYPE_REFUND => '用户退款',
        self::CHANGE_TYPE_CONVERT => '奖励金转换积分',
        self::CHANGE_TYPE_DEVELOPMENT => '每月终生成就奖奖励',
        self::CHANGE_TYPE_THANKFUL => '每日感恩奖',
        self::CHANGE_TYPE_RECOMMEND => '拓展会员奖励'
    ];


    const SYSTEM_TRANSFER_MESSAGE = '系统转账%s积分';

    const USER_CONSUME_MESSAGE = '订单号:%s,用户积分支付%s积分';

    const REBATE_PRIZE_MESSAGE = '订单号:%s,奖励%s元';

    const APPLY_WITHDRAW_MESSAGE = '申请提现,暂时冻结%s元';

    const WITHDRAW_SUCCESS_MESSAGE = '提现成功%s元,手续费%s元';

    const IN_MESSAGE = '转出到用户%s积分%s,手续费%s';

    const OUT_MESSAGE = '用户%s转入积分%s,手续费%s,实入%s积分';

    const REBATE_MESSAGE = '订单%s,%s级返佣获利%s';

    const ACHIEVEMENT_MESSAGE = '每日业绩奖结算:%s元';

    const CONVERT_MESSAGE = '%s元奖励金转换积分,手续费%s';

    const DEVELOPMENT_MESSAGE = '每月终生成就奖结算:%s元,来自于会员编号%s';

    const LEADER_MESSAGE = '每月领导奖结算:%s元';

    const THANKFUL_MESSAGE = '每日感恩奖发放%s积分';

    const RECOMMEND_MESSAGE = '拓展两名会员奖励%s积分';

    const MONEY_TYPE_POINT = 0;

    const MONEY_TYPE_PRIZE = 1;

    const MONEY_TYPE_FLOW = 2;

    const MONEY_TYPE_MAP = [
        self::MONEY_TYPE_POINT => '积分',
        self::MONEY_TYPE_PRIZE => '余额'
    ];

}