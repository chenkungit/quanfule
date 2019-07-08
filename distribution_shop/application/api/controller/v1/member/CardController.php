<?php

namespace app\api\controller\v1\member;

use app\api\exception\ValidateException;
use app\api\validate\Member\CardValidate;
use app\common\controller\ApiController;
use app\common\Lang\Lang;
use app\service\Member\MemberCardInfoService;
use think\Request;
use Zhuzhichao\BankCardInfo\BankCard;

class CardController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
    }

    public function list()
    {
        $list = MemberCardInfoService::service()->getPageList($this->data);

        return $this->respondWithArray($list);
    }

    public function info(int $id)
    {
        $info = MemberCardInfoService::service()->getInfo($id, $this->data['user_id']);

        return $this->respondWithArray(compact('info'));
    }

    public function create()
    {
        $card = new CardValidate();
        $card->scene('create')->check($this->data);

        MemberCardInfoService::service()->create($this->data);

        return $this->respondWithArray([], Lang::CREATE_SUCCESS);
    }

    public function update(int $id)
    {
        $card = new CardValidate();
        $card->scene('update')->check($this->data);

        MemberCardInfoService::service()->update($this->data);

        return $this->respondWithArray([], Lang::UPDATE_SUCCESS);
    }

    public function delete(int $id)
    {

        MemberCardInfoService::service()->delete($id, $this->data['user_id']);

        return $this->respondWithArray([], Lang::DELETE_SUCCESS);

    }


    public function check()
    {

        $this->data['bank_number'];
        $cb = BankCard::info($this->data['bank_number']);

        if($cb['validated'] === false){
            throw new ValidateException('请输入正确的银行卡号');
        }

        return $this->respondWithArray($cb);
    }


}