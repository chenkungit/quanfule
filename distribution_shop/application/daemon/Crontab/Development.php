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

class Development extends Command
{
    protected $server;
    protected $order;

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
            foreach ($res as $item) {

                $parent_ids = Db::name('ds_relation')->where('user_id', $item['user_id'])->column('parent_id');
                foreach ($parent_ids as $parent_id) {
                    if (isset($array[$parent_id])) {
                        $array[$parent_id]['count'] += 1;
                    } else {
                        $array[$parent_id]['count'] = 1;
                    }
                }
            }
            $array_keys = array_keys($array);

            $vipUserCollection = VipUserInfo::getCollectionByUserIds($array_keys, 'user_id,achievement_award');

//            Db::startTrans();
            foreach ($vipUserCollection as $item) {
                $array[$item['user_id']]['prize_money'] = 'prize_money + ' . $array[$item['user_id']]['count'] * $item['achievement_award'];
                $insert[] = [
                    'user_id' => $item['user_id'],
                    'prize_money' => $array[$item['user_id']]['count'] * $item['achievement_award'],
                    'change_type' => AccountLogEnums::CHANGE_TYPE_DEVELOPMENT,
                    'change_desc' => sprintf(AccountLogEnums::DEVELOPMENT_MESSAGE, $array[$item['user_id']]['count'] * $item['achievement_award'])
                ];
                unset($array[$item['user_id']]['count']);
            }

            Db::startTrans();
            try {
                Users::batchUpdate($array, 'user_id');
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