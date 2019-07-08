<?php

namespace app\api\controller\v1;


use app\api\validate\CaptchaValidate;
use app\common\controller\ApiController;
use app\service\CaptchaService;
use think\Request;

class CaptchaController extends ApiController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

    }

    public function create(Request $request)
    {
        $captchaValidate = new CaptchaValidate();
        $captchaValidate->scene('create')->check($request->param());

        $captcha = CaptchaService::service()->create($this->data['device_id']);

        return $this->respondWithArray(compact('captcha'));
    }
}