<?php

namespace app\api\controller\v1\passport;

use app\common\controller\ApiController;
use app\common\Factory\RegisterFactory;
use think\Request;

class AuthController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function signUp()
    {
        
        
//        RegisterFactory::getService();
    }
}