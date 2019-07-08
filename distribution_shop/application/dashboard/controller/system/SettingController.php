<?php

namespace app\dashboard\controller\system;

use app\common\controller\WebController;
use app\common\Lang\Lang;
use app\dashboard\validate\System\SettingValidate;
use app\service\System\SystemSettingService;

use think\Request;

class SettingController extends WebController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function list()
    {
        $list = SystemSettingService::service()->getPageList();

        return $this->respondWithArray($list);
    }


    public function create()
    {
    }

    public function update()
    {
        $settingValidate = new SettingValidate();

        $settingValidate->scene('update')->check($this->data);

        SystemSettingService::service()->update($this->data);

        return $this->respondWithArray([], Lang::UPDATE_SUCCESS);
    }
}