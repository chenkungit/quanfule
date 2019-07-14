<?php

namespace app\daemon\Queue;

use app\common\controller\Refund;
use app\common\Entity\DsRelation;
use app\common\Entity\OrderInfo;
use app\common\Entity\UmbrellaOrderFlow;
use app\common\Entity\UmRelation;
use app\common\Enums\AccountLogEnums;
use app\common\Enums\RedisKeyEnums;
use app\service\Member\AccountService;
use app\service\Vip\VipUserInfoService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;

class Distribution extends Command
{
    protected $cancelState = 0;

    protected function configure()
    {
        $this->setName('distribution:consume')->setDescription('分销统计队列');
    }

    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {

        //开启信号量
        pcntl_signal(SIGUSR1, function ($signo) {
            if ($signo == SIGUSR1) {
                $this->cancelState = 1;
            }
        });

        Log::info("---分销队列启动---");

        while (true) {
            try {

                pcntl_signal_dispatch();
                if ($this->cancelState == 1) {
                    echo "收到停止任务信号，任务结束\n";
                    exit;
                }

                $data = redis()->brPop([RedisKeyEnums::DISTRIBUTION_QUEUE], 1);
                if (!$data) {
                    continue;
                }
                list($queue, $orderId) = $data;

                $orderInfo = OrderInfo::getInfoByOrderId($orderId);

                $amount = $orderInfo->vip_goods_amount;

                if ($orderInfo->is_deal == 1) {
                    Log::info($orderInfo->order_sn . '该订单已经处理过了');
                    return;
                }
                if($amount <= 0){
                    Log::info($orderInfo->order_sn . '该订单非会员商品');
                    return;
                }
                Log::info("---订单:" . $orderInfo->order_sn . "开始---");

                $vipUserInfo = VipUserInfoService::service()->getInfo($orderInfo->user_id);
                if (!$vipUserInfo) {
                    $orderInfo->is_deal = 1;
                    $orderInfo->save();
                    Log::info($orderInfo->user_id . '该用户无会员信息');
                    return;
                }

                $first_reback_rate = $vipUserInfo['first_reback_rate'];
                $second_reback_rate = $vipUserInfo['second_reback_rate'];
                $third_reback_rate = $vipUserInfo['third_reback_rate'];

                $dsRelation = DsRelation::getCollectionByUserId($orderInfo->user_id, 3, 'parent_id,level');

                if (!$dsRelation) {
                    $orderInfo->is_deal = 1;
                    $orderInfo->save();
                    Log::info($orderInfo->user_id . '没有上级,结束流程');
                    return;
                }


                Db::startTrans();
                $set = [];
                //根据相差级数返回佣金
                foreach ($dsRelation as $item) {

                    switch ($item['level']) {
                        case 1:
                            $prize_money = $amount * $first_reback_rate;
                            break;
                        case 2:
                            $prize_money = $amount * $second_reback_rate;
                            break;
                        case 3:
                            $prize_money = $amount * $third_reback_rate;
                            break;
                    }

                    $set[] = [
                        'user_id' => $item['parent_id'],
                        'prize_money' => $prize_money
                    ];

                    Log::info("用户:" . $item['parent_id'] . " " . sprintf(AccountLogEnums::REBATE_MESSAGE, $orderInfo->order_sn, $item['level'], $prize_money));
                    AccountService::service()
                        ->setUserId($item['parent_id'])
                        ->setPrizeMoney($prize_money)
                        ->setChangeType(AccountLogEnums::CHANGE_TYPE_REBATE_PRIZE)
                        ->setChangeDesc(sprintf(AccountLogEnums::REBATE_MESSAGE, $orderInfo->order_sn, $item['level'], $prize_money))
                        ->change();

                }

                $umRelationCollection = UmRelation::getCollectionByUserId($orderInfo->user_id, 'parent_id');

                //会员商品流水插入 业绩流水表  每日凌晨统计业绩奖使用
                if (floatval($orderInfo->vip_goods_amount) > 0) {
                    foreach ($umRelationCollection as &$item) {
                        $item['order_id'] = $orderInfo->order_id;
                        $item['order_sn'] = $orderInfo->order_sn;
                        $item['amount'] = $orderInfo->vip_goods_amount;
                        $item['date'] = date('Y-m-d', $orderInfo->pay_time);
                        $item['user_id'] = $orderInfo->user_id;
                    }
                    $umbrellaOrderFlow = new UmbrellaOrderFlow();
                    $umbrellaOrderFlow->insertAll($umRelationCollection);
                }

                $orderInfo->is_deal = 1;
                $orderInfo->save();

                Log::info($orderInfo->order_sn . '分销统计结束');
                Db::commit();
            } catch (\Exception $exception) {
                Db::rollback();
                if (isset($orderInfo)) {
                    redis()->lPush(RedisKeyEnums::DISTRIBUTION_ERROR_QUEUE, $orderInfo->order_id);
                    echo $orderInfo->order_sn . '出错  ' . $exception->getMessage();
                }
                echo $exception->getMessage();
                Log::info($exception->getFile());
                Log::info($exception->getLine());
                Log::info($exception->getMessage());
                break;
            }
            unset($set);
        }

    }


}