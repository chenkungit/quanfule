<?php

namespace app\api\controller\v1\wechat;


use app\common\controller\ApiController;
use think\Request;

class OauthController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


    public function authorize()
    {
        $oauth = officialAccount()->oauth;

        return $oauth->scopes(['snsapi_userinfo'])->redirect();
    }


    public function callback()
    {
        $oauth = officialAccount()->oauth;
        $res = $oauth->user()->getOriginal();

        dd($res);
    }
}