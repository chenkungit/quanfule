<?php

namespace app\api\controller\v1\distribution;


use app\api\exception\IllegalException;
use app\common\controller\ApiController;
use think\Request;

class RelationController extends ApiController
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);

        if (!$this->is_vip) {
            throw new IllegalException('您还不是会员');
        }
    }

    public function list()
    {

    }
}