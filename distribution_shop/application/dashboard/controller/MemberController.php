<?php

namespace app\dashboard\controller;

use app\common\controller\WebController;
use app\common\Enums\AccountLogEnums;
use app\dashboard\validate\Member\MemberValidate;
use app\service\Member\AccountService;
use app\service\Member\MemberService;
use think\Db;
use think\Request;


class MemberController extends WebController
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function lists()
    {

        $data = MemberService::service()->getPageList(\request()->param());

        return $this->respondWithArray($data);
    }

    public function consumeList()
    {
        $memberValidate = new MemberValidate();


        $memberValidate->scene('consumeList')->check(\request()->param());

        $data = AccountService::service()->getPageList(\request()->param());

        return $this->respondWithArray($data);
    }

    public function add(Request $request)
    {
    }

    public function edit($id)
    {
    }

    public function delete($id)
    {
    }


    //下发积分
    public function sendPoint()
    {

        $memberValidate = new MemberValidate();

        $memberValidate->scene('sendPoint')->check(\request()->param());


        Db::startTrans();
        try {
            AccountService::service()
                ->setUserId($this->data['user_id'])
                ->setUserMoney($this->data['money'])
                ->setChangeType(AccountLogEnums::CHANGE_TYPE_SYSTEM_TRANSFER)
                ->setChangeDesc(sprintf(AccountLogEnums::SYSTEM_TRANSFER_MESSAGE, $this->data['money']))
                ->change();

            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            throw $exception;
        }


        return $this->respondWithArray([], '下发成功');
    }

    public function thankfulSet()
    {
        $this->_validate('user_id', 'money');

        $res = Db::name('thankful_bonus')->where('user_id', $this->data['user_id'])->find();
        if ($res) {
            Db::name('thankful_bonus')->where('id', $res['id'])->update(['money' => $this->data['money']]);
        } else {
            Db::name('thankful_bonus')->insert(['user_id' => $this->data['user_id'], 'money' => $this->data['money']]);
        }
        return $this->respondWithArray([]);
    }


    public function thankfulInfo()
    {
        $this->_validate('user_id');

        $res['info'] = Db::name('thankful_bonus')->where('user_id', $this->data['user_id'])->find();
        return $this->respondWithArray($res);
    }


}
