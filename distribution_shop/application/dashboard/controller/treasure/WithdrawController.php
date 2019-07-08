<?php

namespace app\dashboard\controller\treasure;

use app\common\controller\WebController;
use app\common\Lang\Lang;
use app\service\Treasure\WithdrawService;
use think\Db;
use think\Request;

class WithdrawController extends WebController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function list()
    {
        $data = WithdrawService::service()->getPageList(\request()->param());

        return $this->respondWithArray($data);
    }


    public function finish()
    {

        $this->_validate('id');

        $id = $this->data['id'];

        Db::startTrans();
        try {
            WithdrawService::service()->finish($id);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            throw $exception;
        }
        return $this->respondWithArray([]);
    }

    public function reject()
    {

    }
}