<?php

namespace app\service\Member;


use app\common\Entity\AccountLog;
use app\common\Entity\Users;
use app\common\Enums\AccountLogEnums;
use app\service\BaseService;
use think\Db;

class AccountService extends BaseService
{
    private $userId;

    private $moneyType;

    private $changeType;

    private $changeDesc;

    private $userMoney = 0;

    private $frozenMoney = 0;

    private $prizeMoney = 0;

    private $frozenPrizeMoney = 0;

    public function getPageList(array $requestData)
    {
        $data = AccountLog::getPageList($requestData);
        foreach ($data['list'] as &$item) {
            $item['money_type_name'] = AccountLogEnums::MONEY_TYPE_MAP[$item['money_type']];
            $item['change_type_name'] = AccountLogEnums::CHANGE_TYPE_MAP[$item['change_type']];
        }

        return $data;
    }


    public function change()
    {
        $update = [];

        if ($this->userMoney != 0) {
            $update['user_money'] = Db::raw("user_money+($this->userMoney)");
            $this->moneyType = AccountLogEnums::MONEY_TYPE_POINT;
        }

        if ($this->frozenMoney != 0) {
            $update['frozen_money'] = Db::raw("frozen_money+($this->frozenMoney)");
            $this->moneyType = AccountLogEnums::MONEY_TYPE_POINT;
        }

        if ($this->prizeMoney != 0) {
            $update['prize_money'] = Db::raw("prize_money+($this->prizeMoney)");
            $this->moneyType = AccountLogEnums::MONEY_TYPE_PRIZE;
        }

        if ($this->frozenPrizeMoney != 0) {
            $update['frozen_prize_money'] = Db::raw("frozen_prize_money+($this->frozenPrizeMoney)");
            $this->moneyType = AccountLogEnums::MONEY_TYPE_PRIZE;
        }


        if ($update) {
            Users::update($update, ['user_id' => $this->userId]);
        }

        $this->Log();
    }


    public function Log()
    {

        $accountLog = new AccountLog();

        $accountLog->user_id = $this->userId;
        $accountLog->change_type = $this->changeType;
        $accountLog->change_desc = $this->changeDesc;
        if ($this->userMoney != 0) {
            $accountLog->user_money = $this->userMoney;
        }

        if ($this->frozenMoney != 0) {
            $accountLog->frozen_money = $this->frozenMoney;
        }

        if ($this->prizeMoney != 0) {
            $accountLog->prize_money = $this->prizeMoney;
        }

        if ($this->frozenPrizeMoney != 0) {
            $accountLog->frozen_prize_money = $this->frozenPrizeMoney;
        }
        $accountLog->money_type = $this->moneyType;

        $accountLog->save();
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUserMoney($userMoney)
    {
        $this->userMoney = $userMoney;
        return $this;
    }


    public function setChangeType($changeType)
    {
        $this->changeType = $changeType;
        return $this;
    }

    public function setChangeDesc($changeDesc)
    {
        $this->changeDesc = $changeDesc;
        return $this;
    }

    public function setFrozenMoney($frozenMoney)
    {
        $this->frozenMoney = round($frozenMoney, 2);
        return $this;
    }

    public function setPrizeMoney($prizeMoney)
    {
        $this->prizeMoney = round($prizeMoney, 2);
        return $this;
    }

    public function setFrozenPrizeMoney($frozenPrizeMoney)
    {
        $this->frozenPrizeMoney = round($frozenPrizeMoney, 2);
        return $this;
    }

    public function setMoneyType($moneyType)
    {
        $this->moneyType = $moneyType;
        return $this;
    }


}