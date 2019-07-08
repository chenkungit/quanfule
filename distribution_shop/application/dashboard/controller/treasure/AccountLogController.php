<?php

namespace app\dashboard\controller\treasure;


use app\common\controller\WebController;
use app\common\Entity\Users;
use app\service\Member\AccountService;
use app\service\Vip\VipSettingService;
use app\service\Vip\VipUserInfoService;
use think\Request;

class AccountLogController extends WebController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function list()
    {
        if (isset($this->data['user_name'])) {
            $this->data['user_id'] = Users::where('user_name', 'like', '%' . $this->data['user_name'] . '%')->column('user_id');
        }

        $data = AccountService::service()->getPageList($this->data);
        $user_ids = array_column($data['list'], 'user_id');

        $userCollection = VipUserInfoService::service()->getCollection($user_ids);
        $userCollection = array_column($userCollection, null, 'user_id');

        foreach ($data['list'] as &$item) {
            $item['user_name'] = $userCollection[$item['user_id']]['user_name'];
            $item['vip_code'] = $userCollection[$item['user_id']]['vip_code'];
            $item['vip_setting_name'] = $userCollection[$item['user_id']]['vip_setting_name'];
        }

        return $this->respondWithArray($data);
    }
}