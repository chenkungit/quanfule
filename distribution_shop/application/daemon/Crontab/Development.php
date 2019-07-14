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

class Development extends Command
{
    protected $server;
    protected $order;
    protected $m=[];

    protected function configure()
    {
        $this->setName('development:settle')->setDescription('每月终生成就奖奖励');
    }



    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {
        Log::info("---每月终生成就奖奖励结算开始---");
        //会员有领导奖状态说明上个月消费过一次 遍历查找上级 每个上级+10

        try {
            //先获取上个月所有有领导奖的状态的用户  查出他的上级 每个上级+1  最后根据每个上级的会员等级设置的终生成就奖返回比例
            $lastMonth = date("Y-m", strtotime("-1 month"));

            $res = Db::name('development_status')->field('distinct user_id')->where('expire_date_month', $lastMonth)->cursor();

            if (!$res->current()) {
                Log::info("上个月无记录");
                echo "上个月无记录" . PHP_EOL;
                return;
            }
            $array = [];
            $results = [];
            $dsRelation = new DsRelation();
            foreach ($res as $item) {
                //调用递归函数，查询所有的上级id
                $results =  $dsRelation->getUpUser($item['user_id'],$results);
                print_r($results);
                foreach($results as $rs){
                    $isH=Db::name('development_status')->field('user_id')->where('user_id', $rs)->where('expire_date_month', $lastMonth)->find();
                    if($isH){
                        array_push($array,$rs);
                        $insert[] = [
                            'user_id' => $rs,
                            'prize_money' => 10,
                            'money_type' =>AccountLogEnums::MONEY_TYPE_PRIZE,
                            'change_type' => AccountLogEnums::CHANGE_TYPE_DEVELOPMENT,
                            'change_desc' => sprintf(AccountLogEnums::DEVELOPMENT_MESSAGE, 10,$item['user_id'])
                        ];
                    }
                }
            }   
            //暂时写死 不取配置的值
//          $vipUserCollection = VipUserInfo::getCollectionByUserIds($array, 'user_id,achievement_award');
            $updateinfo=[];
            foreach($array as $r){
                
                //查询userid现在的余额prize_money
                if(!array_key_exists($r,$updateinfo)){
                    $userinfo = Users::getInfoByUserId($r,'user_id,prize_money');
                    $updateinfo[$r]['prize_money'] =$userinfo['prize_money']+10;
                }else{
                    $updateinfo[$r]['prize_money'] =  $updateinfo[$r]['prize_money'] + 10;
                }
            }
            Db::startTrans();
            try {
                Users::batchUpdate($updateinfo, 'user_id');
                $accountLog = new AccountLog();
                $accountLog->insertAll($insert);
                Db::commit();
            } catch (\Exception $exception) {
                Db::rollback();
                throw $exception;
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getLine();
        }

    

        Log::info("---每月终生成就奖奖励结算结束---");
    }

}