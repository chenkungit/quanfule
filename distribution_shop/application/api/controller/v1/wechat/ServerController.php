<?php

namespace app\api\controller\v1\wechat;

use app\common\controller\ApiController;
use think\Request;

class ServerController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


    public function server()
    {
        $server = officialAccount()->server;
        $response = $server->serve();
        $response->send();
    }
}