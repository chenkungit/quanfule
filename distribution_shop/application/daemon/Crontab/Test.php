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
            $dsRelation = new DsRelation();
            //获取所有上级人员
            $user_id = 20;
            $results = [];
            $results =  $dsRelation->getUpUser($user_id,$results);
            print_r($results);
            if($results){
                //添加自己构建完整链
                array_push($results,$user_id);
            }
            $dsRelation->updateAchieveByUser($results,9980);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getLine();
        }

    }

}