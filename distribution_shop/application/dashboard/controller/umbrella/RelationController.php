<?php

namespace app\dashboard\controller\umbrella;

use app\common\controller\WebController;
use app\service\Umbrella\UmRelationService;
use think\Db;
use think\Request;

class RelationController extends WebController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


    public function topUser()
    {

        $res['collection'] = UmRelationService::service()->getTopUser();

        return $this->respondWithArray($res);
    }


    public function downUser()
    {

        $this->_validate('down_user_id');

        $res['collection'] = UmRelationService::service()->getDownUserLoose($this->data['down_user_id']);

        return $this->respondWithArray($res);
    }
}