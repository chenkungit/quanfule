<?php

namespace app\daemon\Crontab;


use app\common\Entity\AccountLog;
use app\common\Entity\Users;
use app\common\Entity\VipUserInfo;
use app\common\Enums\AccountLogEnums;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;

class Leader extends Command
{
    protected $server;
    protected $order;

    protected function configure()
    {
        $this->setName('leader:settle')->setDescription('每月领导奖奖奖励结算');
    }

    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {
        Log::info("---每月领导奖奖奖励结算开始---");
        //按伞下三级人员的业绩奖励计算   一级业绩奖的100% 二级业绩奖的50% 三级业绩奖的20%

        try {

            //先获取上个月所有有领导奖的状态的用户  查出他的上级 每个上级+1  最后根据每个上级的会员等级设置的终生成就奖返回比例
            $lastMonth = date("Y-m", strtotime("-1 month"));

            /* @var \Generator $res */
            $res = Db::name('development_status')->field('distinct user_id')->where('expire_date_month', $lastMonth)->cursor();
            if (!$res->current()) {
                Log::info("上个月无记录");
                echo "上个月无记录" . PHP_EOL;
                return;
            }
            $update = [];
            $insert = [];
            foreach ($res as $item) {
                $leader_prize = 0;
                $tmp = [];
                $children_collection = Db::name('ds_relation')
                    ->field('user_id,level')
                    ->where('parent_id', $item['user_id'])
                    ->whereIn('level', [1, 2, 3])
                    ->select();

                foreach ($children_collection as $item2) {
                    $tmp[$item2['level']][] = $item2['user_id'];
                }
                foreach ($tmp as $key => $item3) {
                    $total_prize_amount = Db::name('achievement_flow')
                        ->where('date_month', $lastMonth)
                        ->whereIn('user_id', $item3)
                        ->sum('prize_amount');
                    switch ($key) {
                        case 1:
                            $leader_prize += $total_prize_amount;
                            echo "一级上个月领导奖流水" . $total_prize_amount . PHP_EOL;
                            break;
                        case 2:
                            $leader_prize += $total_prize_amount * 0.5;
                            echo "二级上个月领导奖流水" . $total_prize_amount . PHP_EOL;
                            break;
                        case 3:
                            $leader_prize += $total_prize_amount * 0.2;
                            echo "三级上个月领导奖流水" . $total_prize_amount . PHP_EOL;
                            break;
                    }
                }
                if ($leader_prize > 0) {
                    $update[$item['user_id']]['prize_money'] = 'prize_money + ' . $leader_prize;
                    $insert[] = [
                        'user_id' => $item['user_id'],
                        'prize_money' => $leader_prize,
                        'change_type' => AccountLogEnums::CHANGE_TYPE_LEADER_PRIZE,
                        'change_desc' => sprintf(AccountLogEnums::LEADER_MESSAGE, $leader_prize)
                    ];
                }
            }
            Db::startTrans();
            try {
                Users::batchUpdate($update, 'user_id');
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