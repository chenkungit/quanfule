<?php

namespace app\service\Member;

use app\common\Entity\MemberCardInfo;
use app\service\BaseService;


class MemberCardInfoService extends BaseService
{

    public function getPageList(array $requestData)
    {
        return MemberCardInfo::getPageList($requestData);

    }

    /* @return MemberCardInfo */
    public function getInfo(int $id, int $userId)
    {
        $data = MemberCardInfo::getInfoByIdWithUserId($id, $userId);

        return $data;
    }


    public function create(array $data)
    {
        MemberCardInfo::create($data);
    }

    public function update(array $set)
    {
        $memberCardInfo = MemberCardInfo::getInfoByIdWithUserId($set['id'], $set['user_id']);

        $memberCardInfo->save($set);
    }


    public function delete(int $id, int $user_id)
    {
        $memberCardInfo = MemberCardInfo::getInfoByIdWithUserId($id, $user_id);
        $memberCardInfo->is_delete = 1;
        $memberCardInfo->save();
    }
}