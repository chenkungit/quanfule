<?php

namespace app\service\Umbrella;

use app\api\exception\IllegalException;
use app\common\Entity\UmRelation;
use app\common\model\BaseModel;
use app\service\BaseService;
use app\service\Vip\VipUserInfoService;
use com\Tree;
use function GuzzleHttp\Promise\iter_for;
use think\Db;

class UmRelationService extends BaseService
{
    public function maintain(int $parentId, int $userId)
    {

        $set[] = [
            'user_id' => $userId,
            'parent_id' => $parentId,
            'level' => 1
        ];

        $parentCollection = UmRelation::getCollectionByUserId($parentId, 'parent_id,level');
        if ($parentCollection) {
            foreach ($parentCollection as $item) {
                $set[] = [
                    'user_id' => $userId,
                    'parent_id' => $item['parent_id'],
                    'level' => $item['level'] + 1
                ];
            }
        }

        $umRelation = new UmRelation();
        $umRelation->saveAll($set);

    }


    public function getTopUser()
    {
        $res = Db::query("select distinct parent_id as user_id,u.user_name,vui.vip_code,vs.name as vip_setting_name from ecs_um_relation ur
                LEFT JOIN ecs_users u on ur.parent_id = u.user_id
                LEFT JOIN ecs_vip_user_info vui on ur.parent_id = vui.user_id
                LEFT JOIN ecs_vip_setting vs on vui.vs_id = vs.id
                where parent_id not in (select user_id from ecs_um_relation)");

        return $res;
    }


    public function getDownUserLoose(int $downUserId)
    {
        $umRelationUid = UmRelation::field('user_id')->where('parent_id', $downUserId)->where('level', 1)->column('user_id');

        if (!$umRelationUid) {
            return [];
        }
        $data = VipUserInfoService::service()->getCollection($umRelationUid);

        return $data;
    }

    public function getDownUser(int $userId, int $downUserId = 0)
    {
        if (!$downUserId) {
            $umRelationUid = UmRelation::field('user_id')->where('parent_id', $userId)->where('level', 1)->column('user_id');
        } else {
            //接口层杜绝非法请求
            if (!UmRelation::field('user_id')->where('user_id', $downUserId)->where('parent_id', $userId)->find()) {
                throw new IllegalException('非法请求');
            }

            $umRelationUid = UmRelation::field('user_id')->where('parent_id', $downUserId)->where('level', 1)->column('user_id');
        }

        if (!$umRelationUid) {
            return [];
        }
        $data = VipUserInfoService::service()->getCollection($umRelationUid);

        return $data;
    }
}