<?php

namespace app\daemon\Crontab;


use app\common\Entity\AccountLog;
use app\common\Entity\Users;
use app\common\Entity\DsRelation;
use app\common\Entity\VipUserInfo;
use app\common\Enums\AccountLogEnums;
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
            $userids = [7,6,1,3];
            $achieve = 21;

            foreach($userids as $user_id){
                Db::table('ecs_ds_relation')->where(['user_id'=>$user_id,'level'=>1])->update(['current_achieve'=>Db::raw('current_achieve+'.$achieve)]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getLine();
        }

    }

}