<?php

namespace app\api\controller\v1\member;


use app\api\exception\IllegalException;
use app\api\exception\ValidateException;
use app\api\validate\Member\PointValidate;
use app\common\controller\ApiController;
use app\common\Entity\UmRelation;
use app\common\Entity\Users;
use app\common\Enums\AccountLogEnums;
use app\common\Enums\SystemSettingEnums;
use app\service\Member\AccountService;
use app\service\Member\MemberCardInfoService;
use app\service\Member\MemberService;
use app\service\System\SystemSettingService;
use app\service\Treasure\WithdrawService;
use think\Db;
use think\Request;

class TreasureController extends ApiController
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);

    }

    public function info()
    {

        $data = MemberService::service()->getTreasure($this->data['user_id']);


        return $this->respondWithArray($data);
    }

    public function transfer()
    {
        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }

        $pointValidate = new PointValidate();

        $pointValidate->scene('transfer')->check(\request()->param());


        $in_point = $this->data['point'];
        $service_charge = $in_point * SystemSettingService::service()->getTransferServiceChargeRate();
        $out_point = $this->data['point'] + $service_charge;
        //接口层杜绝非法请求
        if (!UmRelation::field('user_id')->where('user_id', $this->data['to_user_id'])->where('parent_id', $this->data['user_id'])->find()) {
            throw new IllegalException('非法请求');
        }


        $treasureInfo = MemberService::service()->getTreasure($this->data['user_id']);

        if ($treasureInfo['user_money'] < $out_point) {
            throw new ValidateException('积分不足' . $out_point);
        }

        $userCollection = Users::getCollectionByUserId([$this->data['user_id'], $this->data['to_user_id']], 'user_id,user_name');
        $userCollection = array_column($userCollection, null, 'user_id');
        Db::startTrans();
        try {
            AccountService::service()
                ->setUserId($this->data['user_id'])
                ->setUserMoney(-$out_point)
                ->setChangeType(AccountLogEnums::CHANGE_TYPE_USER_OUT)
                ->setChangeDesc(sprintf(AccountLogEnums::IN_MESSAGE, $userCollection[$this->data['to_user_id']]['user_name'], $in_point, $service_charge))
                ->change();


            AccountService::service()
                ->setUserId($this->data['to_user_id'])
                ->setUserMoney($in_point)
                ->setChangeType(AccountLogEnums::CHANGE_TYPE_USER_IN)
                ->setChangeDesc(sprintf(AccountLogEnums::OUT_MESSAGE, $userCollection[$this->data['user_id']]['user_name'], $out_point, $service_charge, $in_point))
                ->change();

            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            throw $exception;
        }

        return $this->respondWithArray([], '转出成功');
    }

    public function flowList()
    {
        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }

        $data = AccountService::service()->getPageList($this->data);
        foreach ($data['list'] as &$item) {
            $item['created_time'] = date('m.d', strtotime($item['created_time']));
        }

        return $this->respondWithArray($data);
    }

    public function transferList()
    {
        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }

        $this->data['change_type'] = [AccountLogEnums::CHANGE_TYPE_USER_OUT, AccountLogEnums::CHANGE_TYPE_USER_IN];

        $data = AccountService::service()->getPageList($this->data);

        return $this->respondWithArray($data);
    }

    public function convert()
    {
        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }

        $pointValidate = new PointValidate();

        $pointValidate->scene('convert')->check(\request()->param());


        $treasureInfo = MemberService::service()->getTreasure($this->data['user_id']);

        $in_point = $this->data['money'];
        $service_charge = $in_point * SystemSettingService::service()->getSystemFromRedis(SystemSettingEnums::CONVERT_SERVICE_CHARGE_RATE);
        $out_point = $this->data['money'] + $service_charge;

        if ($treasureInfo['prize_money'] < $out_point) {
            throw new ValidateException('可转换金额不足' . $out_point);
        }


        Db::startTrans();
        try {
            $accountService = new AccountService();
            $accountService
                ->setUserId($this->data['user_id'])
                ->setPrizeMoney(-$out_point)
                ->setChangeType(AccountLogEnums::CHANGE_TYPE_CONVERT)
                ->setChangeDesc(sprintf(AccountLogEnums::CONVERT_MESSAGE, $out_point, $service_charge))
                ->change();
            $accountService = new AccountService();
            $accountService
                ->setUserId($this->data['user_id'])
                ->setUserMoney($in_point)
                ->setChangeType(AccountLogEnums::CHANGE_TYPE_CONVERT)
                ->setChangeDesc(sprintf(AccountLogEnums::CONVERT_MESSAGE, $out_point, $service_charge))
                ->change();

            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            throw $exception;
        }

        return $this->respondWithArray([], '转换成功');
    }

    public function withdrawApply()
    {
        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }

        $pointValidate = new PointValidate();

        $pointValidate->scene('withdrawApply')->check(\request()->param());

        $treasureInfo = MemberService::service()->getTreasure($this->data['user_id']);

        $withdraw_money = $this->data['money'];
        $withdraw_service_charge_money = $withdraw_money * SystemSettingService::service()->getWithdrawServiceChargeRate();
        $withdraw_out_money = $withdraw_money + $withdraw_service_charge_money;

        if ($treasureInfo['prize_money'] < $withdraw_out_money) {
            throw new ValidateException('可提现金额不足' . $withdraw_out_money);
        }

        $memberCardInfo = MemberCardInfoService::service()->getInfo($this->data['member_card_id'], $this->data['user_id']);
        if (!$memberCardInfo) {
            throw new ValidateException('该卡号信息不存在');
        }

        Db::startTrans();
        try {
            WithdrawService::service()->apply($withdraw_money, $withdraw_service_charge_money, $memberCardInfo);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            throw $exception;
        }

        return $this->respondWithArray([], '申请提现成功');
    }


    public function withdrawApplyList()
    {
        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }

        $data = WithdrawService::service()->getPageList($this->data);

        return $this->respondWithArray($data);
    }
}