<?php

namespace app\service\Treasure;

use app\api\exception\IllegalException;
use app\common\Entity\MemberCardInfo;
use app\common\Entity\MemberWithdrawCollection;
use app\common\Entity\Users;
use app\common\Enums\AccountLogEnums;
use app\common\Enums\WithdrawEnums;

use app\service\BaseService;
use app\service\Member\AccountService;

class WithdrawService extends BaseService
{
    public function getPageList(array $pageData)
    {
        $data = MemberWithdrawCollection::getPageList($pageData);

        $user_ids = array_column($data['list'], 'user_id');

        $userCollection = Users::getCollectionByUserId($user_ids, 'user_id,user_name,nick');
        $userCollection = array_column($userCollection, null, 'user_id');

        foreach ($data['list'] as &$item) {
            $item['user_name'] = $userCollection[$item['user_id']]['user_name'];
            $item['status_name'] = WithdrawEnums::STATUS_MAP[$item['status']];
        }
        return $data;
    }

    public function apply(float $withdrawMoney,float $withdraw_service_charge_money,$memberCardInfo)
    {
        AccountService::service()
            ->setUserId($memberCardInfo->user_id)
            ->setPrizeMoney(-($withdrawMoney+$withdraw_service_charge_money))
            ->setFrozenPrizeMoney($withdrawMoney+$withdraw_service_charge_money)
            ->setChangeType(AccountLogEnums::CHANGE_TYPE_APPLY_WITHDRAW)
            ->setChangeDesc(sprintf(AccountLogEnums::APPLY_WITHDRAW_MESSAGE, ($withdrawMoney+$withdraw_service_charge_money)))
            ->change();


        $memberWithdrawCollection = new MemberWithdrawCollection();

        $memberWithdrawCollection->user_id = $memberCardInfo->user_id;
        $memberWithdrawCollection->withdraw_money = $withdrawMoney;
        $memberWithdrawCollection->withdraw_service_charge_money = $withdraw_service_charge_money;
        $memberWithdrawCollection->type = $memberCardInfo->type;
        $memberWithdrawCollection->alipay_account = $memberCardInfo->alipay_account;
        $memberWithdrawCollection->bank_name = $memberCardInfo->bank_name;
        $memberWithdrawCollection->bank_number = $memberCardInfo->bank_number;
        $memberWithdrawCollection->status = WithdrawEnums::STATUS_ONGOING;

        $memberWithdrawCollection->save();
    }


    public function finish($id)
    {

        $memberWithDrawCollection = MemberWithdrawCollection::getInfoById($id);

        if ($memberWithDrawCollection->status != WithdrawEnums::STATUS_ONGOING) {
            throw new IllegalException('非法请求');
        }

        $withdraw_out_money = $memberWithDrawCollection->withdraw_money + $memberWithDrawCollection->withdraw_service_charge_money;

        AccountService::service()
            ->setUserId($memberWithDrawCollection->user_id)
            ->setChangeType(AccountLogEnums::CHANGE_TYPE_WITHDRAW_SUCCESS)
            ->setChangeDesc(sprintf(AccountLogEnums::WITHDRAW_SUCCESS_MESSAGE, $memberWithDrawCollection->withdraw_money, $memberWithDrawCollection->withdraw_service_charge_money))
            ->setFrozenPrizeMoney(-$withdraw_out_money)
            ->change();

        $memberWithDrawCollection->status = WithdrawEnums::STATUS_FINISH;
        $memberWithDrawCollection->save();
    }

    public function reject(){

    }
}