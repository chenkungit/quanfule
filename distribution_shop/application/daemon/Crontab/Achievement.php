<?php

namespace app\daemon\Crontab;
use app\common\Entity\AccountLog;
use app\common\Entity\Users;
use app\common\Enums\AccountLogEnums;
use app\service\Member\AccountService;
use app\service\Vip\VipUserInfoService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;

class Achievement extends Command
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

        //查询所有有至少直接分支的节点（只有2个分支以上才有业绩奖励）
        $achieveUsers = Db::table('ecs_ds_relation')->field('parent_id')->where('level',1)->group('parent_id')->having('count(parent_id)>=2')->select();
        if (!$achieveUsers) {
            Log::info("---没有满足条件的人员---");
            return;
        }

        try {
            foreach($achieveUsers as $user){
                //查询此人线下一级人员信息
                $subInfo = Db::table('ecs_ds_relation')->field('id,current_achieve')->where('level =1 and current_achieve > 0 and parent_id = '.$user['parent_id'])->select();
                //如果有两个分支则执行碰撞操作
                Db::startTrans();
                try{
                    if(count($subInfo)>=2) {
                        //获取业绩奖励比例及用户信息
                        $vipInfo = Db::table('ecs_vip_user_info')->alias('u')->field('v.capping_rate as capping_rate,v.capping_price as capping_price,u.user_id as user_id')
                                    ->leftJoin('ecs_vip_setting v','v.id = u.vs_id')
                                    ->where('u.user_id',$user['parent_id'])
                                    ->find();
                        if($vipInfo){
                            //业绩奖励金额
                            $achieve = $subInfo[0]['current_achieve'] >= $subInfo[1]['current_achieve'] ? $subInfo[1]['current_achieve'] : $subInfo[0]['current_achieve'];
                            //执行碰撞
                            $subInfo[0]['current_achieve'] = $subInfo[0]['current_achieve'] - $achieve;
                            $subInfo[1]['current_achieve'] = $subInfo[1]['current_achieve'] - $achieve;
                            //超封顶则按封顶
                            $achieve_price = $achieve*$vipInfo['capping_rate'] <= $vipInfo['capping_price'] ? $achieve*$vipInfo['capping_rate'] : $vipInfo['capping_price'];
                             //放入业绩奖励流水表
                            $achieveFlow = [
                                'user_id' => $user['parent_id'],
                                'prize_amount' => $achieve_price,
                                'date_month' =>date("Y-m"),
                                'date' => date('Y-m-d'),
                                'remark' => sprintf(AccountLogEnums::ACHIEVEMENT_MESSAGE, $achieve_price,$subInfo[0]['current_achieve']+$achieve,$subInfo[1]['current_achieve']+$achieve,$achieve)
                            ];

                            //奖励日志
                            $insert = [
                                'user_id' => $user['parent_id'],
                                'prize_money' => $achieve_price,
                                'money_type' =>AccountLogEnums::MONEY_TYPE_PRIZE,
                                'change_type' => AccountLogEnums::CHANGE_TYPE_ACHIEVEMENT_AWARD,
                                'change_desc' => sprintf(AccountLogEnums::ACHIEVEMENT_MESSAGE, $achieve_price,$subInfo[0]['current_achieve']+$achieve,$subInfo[1]['current_achieve']+$achieve,$achieve)
                            ];
                            $accountLog = new AccountLog();
                            $accountLog->insert($insert);
                            Db::table('ecs_achievement_flow')->insert($achieveFlow);
                            Db::table('ecs_ds_relation')->update($subInfo[0]);
                            Db::table('ecs_ds_relation')->update($subInfo[1]);
                            //更新余额信息
                            Db::table('ecs_users')
                            ->where('user_id', $user['parent_id'])
                            ->update([
                                'prize_money'  => Db::raw('prize_money+'.$achieve_price)
                            ]);
                        }
                    }
                    Db::commit();
                }catch (\Exception $exception) {
                    Db::rollback();
                    throw $exception;
                }

            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getLine();
        }


        Log::info("---每日业绩奖结算结束---");
    }


}