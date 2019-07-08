<?php

namespace app\dashboard\validate\Vip;

use app\api\validate\BaseValidate;

class SettingValidate extends BaseValidate
{

    protected $rule = [
        'name' => 'require',
        'level' => 'require|number',
        'discount' => 'require|egt:0.01|elt:1',
        'first_reback_rate' => 'require|egt:0.01|elt:1',
        'second_reback_rate' => 'require|egt:0.01|elt:1',
        'third_reback_rate' => 'require|egt:0.01|elt:1',
        'capping_price' => 'require|float',
        'capping_rate' => 'require|egt:0.01|elt:1',
        'achievement_award' => 'require|float'
    ];

    protected $message = [
        'name.require' => '请输入会员等级名称',
        'level.require' => '请输入会员等级',
        'discount.require' => '请输入会员折扣',
        'first_reback_rate.require' => '请输入向上级返佣比例(0.01~0.99)',
        'first_reback_rate.egt' => '请输入向上级返佣比例(0.01~0.99)',
        'first_reback_rate.elt' => '请输入向上级返佣比例(0.01~0.99)',
        'second_reback_rate.require' => '请输入向上上级返佣比例(0.01~0.99)',
        'second_reback_rate.egt' => '请输入向上上级返佣比例(0.01~0.99)',
        'second_reback_rate.elt' => '请输入向上上级返佣比例(0.01~0.99)',
        'third_reback_rate.require' => '请输入向上上上级返佣比例(0.01~0.99)',
        'third_reback_rate.egt' => '请输入向上上上级返佣比例(0.01~0.99)',
        'third_reback_rate.elt' => '请输入向上上上级返佣比例(0.01~0.99)',
        'capping_price.require' => '请输入业绩封顶金额/日',
        'capping_rate.require' => '请输入业绩封顶奖比例',
        'achievement_award.require' => '请输入终身成就奖/月'
    ];

    protected $scene = [
        'create' => ['name', 'level', 'discount', 'first_reback_rate', 'second_reback_rate', 'third_reback_rate', 'capping_rate', 'capping_price', 'achievement_award'],
        'update' => ['name', 'level', 'discount', 'first_reback_rate', 'second_reback_rate', 'third_reback_rate', 'capping_rate', 'capping_price', 'achievement_award'],
    ];
}