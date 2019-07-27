<?php

namespace app\daemon\Crontab;


use app\common\Entity\AccountLog;
use app\common\Entity\Users;
use app\common\Entity\SystemSetting;
use app\common\Entity\VipUserInfo;
use app\common\Enums\AccountLogEnums;
use app\service\Member\AccountService;
use app\common\Enums\SystemSettingEnums;
use app\service\System\SystemSettingService;
use app\service\Development\DevelopmentStatusService;
use app\common\Entity\DsRelation;
use app\common\Enums\RedisKeyEnums;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;
use app\dashboard\controller\umbrella\RelationController;
use app\service\Umbrella\UmRelationService;

class Test extends Command
{
    protected $server;
    protected $order;
    protected $m=[];

    protected function configure()
    {
        $this->setName('test:settle')->setDescription('测试command');
    }



    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {

    try {
            //更新余额信息
        $money  = 1;
        Db::table('ecs_users')
        ->where('user_id', 37)
        ->update([
            'prize_money'  => Db::raw('prize_money+'.$money)
        ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getLine();
        }

    }

}