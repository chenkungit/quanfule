<?php

namespace app\service\Distribution;


use app\common\Entity\DsRelation;
use app\service\BaseService;

class DsRelationService extends BaseService
{
    public function maintain(int $parentId, int $userId)
    {

        $set[] = [
            'user_id' => $userId,
            'parent_id' => $parentId,
            'level' => 1
        ];

        $parentCollection = DsRelation::getCollectionByUserId($parentId, 2, 'parent_id,level');
        if ($parentCollection) {
            foreach ($parentCollection as $item) {
                $set[] = [
                    'user_id' => $userId,
                    'parent_id' => $item['parent_id'],
                    'level' => $item['level'] + 1
                ];
            }
        }
        /*删除默认为顶级的关系数据 */
        $dsRelation = new DsRelation();
        $dsRelation->deleteDefaultRelation($userId);
        $dsRelation->saveAll($set);
    }
}