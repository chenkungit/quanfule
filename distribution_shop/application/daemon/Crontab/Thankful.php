<?php

namespace app\daemon\Crontab;

use app\common\controller\Refund;
use app\common\Entity\UmRelation;
use app\common\Entity\VipSetting;
use app\common\Enums\AccountLogEnums;
use app\common\Enums\RedisKeyEnums;
use app\common\model\Bonus;
use app\common\model\Goods;
use app\common\model\Users;
use app\service\Distribution\DistributionService;
use app\service\Member\AccountService;
use app\service\Member\MemberService;
use app\service\System\SystemSettingService;
use app\service\Vip\VipSettingService;
use app\service\Vip\VipUserInfoService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Order;
use think\Db;
use think\facade\Log;

class Thankful extends Command
{
    protected $server;
    protected $order;

    protected function configure()
    {
        $this->setName('thankful:settle')->setDescription('每日感恩奖发放');
    }

    // 设置命令返回信息
    protected function execute(Input $input, Output $output)
    {
        Log::info("---每日感恩奖发放开始---");


        try {
            $res = Db::name('thankful_bonus')->field('id,user_id,money,send_money')->cursor();
            if (!$res->current()) {
                Log::info("表中无记录");
                echo "表中无记录" . PHP_EOL;
                return;
            }


            foreach ($res as $item) {

                if ($item['send_money'] >= $item['money']) {
                    Log::error($item['user_id'] . '感恩奖发送完毕');
                    continue;
                }

                Db::startTrans();
                try {
                    $money = $item['money'] * SystemSettingService::service()->getThankfulRate();

                    Db::name('thankful_bonus')->where('id', $item['id'])->update(['send_money' => Db::raw('send_money + ' . $money)]);


                    AccountService::service()
                        ->setUserId($item['user_id'])
                        ->setChangeDesc(sprintf(AccountLogEnums::THANKFUL_MESSAGE, $money))
                        ->setChangeType(AccountLogEnums::CHANGE_TYPE_THANKFUL)
                        ->setUserMoney($money)
                        ->change();

                    Db::commit();
                } catch (\Exception $exception) {
                    Db::rollback();
                    Log::error($exception->getMessage());
                    break;
                }
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
        }


        Log::info("---每日感恩奖发放结束---");
    }


}