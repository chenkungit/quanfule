<?php

namespace app\common\Entity;


use app\common\model\BaseModel;
use app\common\Utils\Request;

/**
 * 分销上下级关系表
 * @property int $user_id
 * @property int $type
 * @property string $alipay_account
 * @property string $bank_name
 * @property string $bank_number
 * @property float $withdraw_money
 * @property float $withdraw_service_charge_money
 * @property  int $status
 */
class MemberWithdrawCollection extends BaseModel
{
    protected $table = 'ecs_member_withdraw_collection';


    public static function getPageList(array $pageData = [])
    {

        $page = Request::getPage();
        $pageSize = Request::getLimit();

        $ar = self::field('id,user_id,withdraw_money,withdraw_service_charge_money,type,alipay_account,bank_name,bank_number,status,created_time,updated_time');


        if(isset($pageData['user_id'])){
            $ar->where('user_id',$pageData['user_id']);
        }

        $result['count'] = $ar->count('id');
        $result['pagecount'] = ceil(($result['count'] / $pageSize));
        $result['list'] = $ar->page($page, $pageSize)->order('status asc,created_time asc')->select()->toArray();

        return $result;
    }


    /* @return MemberWithdrawCollection */
    public static function getInfoById(int $id)
    {
        return static
            ::where('id', $id)
            ->find();
    }
}