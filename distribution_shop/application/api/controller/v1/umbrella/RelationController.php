<?php
namespace app\api\controller\v1\umbrella;


use app\api\exception\IllegalException;
use app\common\controller\ApiController;
use app\service\Umbrella\UmRelationService;
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

    public function getDownUser()
    {
        $this->data['down_user_id'] = isset($this->data['down_user_id']) ? $this->data['down_user_id'] : 0;

        $data['collection'] = UmRelationService::service()->getDownUser($this->data['user_id'], $this->data['down_user_id']);

        return $this->respondWithArray($data);
    }
}