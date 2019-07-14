<?php

namespace app\daemon\Crontab;

use app\common\Enums\AccountLogEnums;
use app\service\Member\AccountService;
use app\service\Vip\VipUserInfoService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;

class Achievement1 extends Command
{
    protected $server;
    protected $order;

    protected function configure()
    {
        $this->setName('achievement:settle')->setDescription('每日业绩奖结算');
    }

    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {
        Log::info("---每日业绩奖结算开始---");


        //先获取昨天所有伞流水的 parent_id 在遍历计算
        $yesterdayDate = date("Y-m-d", strtotime("-1 day"));
        $dateMonth = date("Y-m", strtotime("-1 day"));
        $parent_ids = Db::name('umbrella_order_flow')->distinct('parent_id')
            ->where('date', $yesterdayDate)
            ->where('status', 0)
            ->column('parent_id');


        if (!$parent_ids) {
            Log::info("---昨日无流水---");
            return;
        }

        try {
            foreach ($parent_ids as $parent_id) {
                echo "执行" . $parent_id . '的业绩奖计算' . PHP_EOL;
                $remark = '';
                //获取该会员所有伞下分支的大数据  先拿到最下级的会员 通过这个会员获取这条直线的所有会员
                $downUserCollection = Db::table('ecs_um_relation a1')
                    ->field('a1.user_id,a1.level')
                    ->leftJoin('ecs_um_relation a2', 'a1.user_id = a2.parent_id')
                    ->where('a1.parent_id', $parent_id)
                    ->where('a2.user_id', null)
                    ->select();

                $amount_array = [];
                //所有伞的最底层会员  统计所有线的流水  获取流水第二的分支
                foreach ($downUserCollection as $downUserInfo) {
                    //先将最底层会员塞入数组
                    $umbrellaUIds = [$downUserInfo['user_id']];

                    //是第一级的话则无上级
                    if ($downUserInfo['level'] == 1) {

                        $amount = Db::table('ecs_umbrella_order_flow')
                            ->whereIn('user_id', $umbrellaUIds)
                            ->where('parent_id', $parent_id)
                            ->sum('amount');

                        if ($amount > 0) {
//                            $amount_array[] = $amount;
                            $amount_array[] = ['amount' => $amount, 'umbrellaUids' => $umbrellaUIds];
                            $remark .= "伞下" . implode(',', $umbrellaUIds) . '流水为' . $amount . PHP_EOL;
                            echo "伞下" . implode(',', $umbrellaUIds) . '流水为' . $amount . PHP_EOL;
                        }
                    } else {

                        //不是最下级 则获取该下级的所有上级 排除 最高级  则是这条线的所有的伞下会员
                        $down_parent_ids = Db::table('ecs_um_relation')->where('user_id', $downUserInfo['user_id'])->order('level desc')->column('parent_id');

                        unset($down_parent_ids[array_search($parent_id, $down_parent_ids)]);
                        $umbrellaUIds = array_merge($umbrellaUIds, $down_parent_ids);

                        $amount = Db::table('ecs_umbrella_order_flow')
                            ->whereIn('user_id', $umbrellaUIds)
                            ->where('parent_id', $parent_id)
                            ->sum('amount');

                        if ($amount > 0) {
                            $amount_array[] = ['amount' => $amount, 'umbrellaUids' => $umbrellaUIds];
                            $remark .= "伞下" . implode(',', $umbrellaUIds) . '流水为' . $amount . PHP_EOL;
                            echo "伞下" . implode(',', $umbrellaUIds) . '流水为' . $amount . PHP_EOL;
                        }
                    }
                }
                if (count($amount_array) < 2) {
                    Db::name('umbrella_order_flow')->where('date', $yesterdayDate)->where('parent_id', $parent_id)->update(['status' => 1]);
                    echo "该会员:" . $parent_id . "伞下有效流水少于2条" . PHP_EOL;
                    Log::info("该会员:" . $parent_id . "伞下有效流水少于2条");
                    continue;
                }
                //排序 获取第二大流水的伞
                $amounts = array_column($amount_array, 'amount');
                array_multisort($amount_array, SORT_DESC, $amounts);

                $yesterday_amount = Db::table('ecs_umbrella_order_flow')
                    ->whereIn('user_id', $amount_array[1]['umbrellaUids'])
                    ->where('parent_id', $parent_id)
                    ->where('date', $yesterdayDate)
                    ->sum('amount');
                if ($yesterday_amount <= 0) {
                    Db::name('umbrella_order_flow')->where('date', $yesterdayDate)->where('parent_id', $parent_id)->update(['status' => 1]);
                    echo "该会员:" . $parent_id . "昨日流水" . $yesterday_amount;
                    Log::info("该会员:" . $parent_id . "昨日流水" . $yesterday_amount);
                    continue;
                }
                //统计到所有伞下金额 去除0后获取第二大流水 * 10%

                $vipUserInfo = VipUserInfoService::service()->getInfo($parent_id);

                $second_amount = $yesterday_amount * $vipUserInfo['capping_rate'];

                if ($second_amount < $vipUserInfo['capping_price']) {
                    $prize_amount = $second_amount;
                } else {
                    $prize_amount = $vipUserInfo['capping_price'];
                }

                Db::startTrans();
                try {
                    Db::table('ecs_achievement_flow')->insert(['user_id' => $parent_id, 'prize_amount' => $prize_amount, 'date' => $yesterdayDate, 'date_month' => $dateMonth, 'remark' => $remark]);

                    AccountService::service()
                        ->setUserId($parent_id)
                        ->setChangeDesc(sprintf(AccountLogEnums::ACHIEVEMENT_MESSAGE, $prize_amount))
                        ->setChangeType(AccountLogEnums::CHANGE_TYPE_ACHIEVEMENT_AWARD)
                        ->setPrizeMoney($prize_amount)
                        ->change();

                    Db::name('umbrella_order_flow')->where('date', $yesterdayDate)->where('parent_id', $parent_id)->update(['status' => 1]);
                    Db::commit();
                } catch (\Exception $exception) {
                    Db::rollback();
                    Log::error($parent_id . '更新出错');
                    Log::error($exception->getMessage());
                    break;
                }
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            echo $parent_id . PHP_EOL;
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getLine();
        }


        Log::info("---每日业绩奖结算结束---");
    }


}