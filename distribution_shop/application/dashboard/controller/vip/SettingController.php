<?php

namespace app\dashboard\controller\vip;

use app\common\controller\WebController;
use app\common\Lang\Lang;
use app\dashboard\validate\Vip\SettingValidate;
use app\service\Vip\VipSettingService;
use think\Request;

class SettingController extends WebController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function list()
    {
        $list = VipSettingService::service()->getPageList($this->data);

        return $this->respondWithArray($list);
    }


    public function create()
    {
        $settingValidate = new SettingValidate();

        $settingValidate->scene('create')->check($this->data);

        VipSettingService::service()->create($this->data);

        return $this->respondWithArray([], Lang::CREATE_SUCCESS);
    }

    public function update()
    {
        $settingValidate = new SettingValidate();

        $settingValidate->scene('update')->check($this->data);

        VipSettingService::service()->update($this->data);

        return $this->respondWithArray([], Lang::UPDATE_SUCCESS);
    }
}