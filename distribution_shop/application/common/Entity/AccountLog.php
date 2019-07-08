<?php

namespace app\common\Entity;

use app\common\model\BaseModel;
use app\common\Utils\Request;


/**
 * 用户金额变动日志表
 * @property int $user_id
 * @property  float $user_money
 * @property  float $frozen_money
 * @property  float $prize_money
 * @property  float $frozen_prize_money
 * @property  string $change_desc
 * @property int $change_type
 * @property int $money_type
 */
class AccountLog extends BaseModel
{
    protected $table = 'ecs_account_log';


    public static function getPageList(array $pageData = [])
    {
        $page = Request::getPage();
        $pageSize = Request::getLimit();

        $ar = static::field('user_id,money_type,user_money,prize_money,change_desc,change_type,created_time');

        if (isset($pageData['money_type'])) {
            $ar->where('money_type', $pageData['money_type']);
        }

        if (isset($pageData['user_id'])) {
            if (is_array($pageData['user_id'])) {
                $ar->whereIn('user_id', $pageData['user_id']);
            } else {
                $ar->where('user_id', $pageData['user_id']);
            }
        }

        if (isset($pageData['change_type'])) {
            if (is_array($pageData['change_type'])) {
                $ar->whereIn('change_type', $pageData['change_type']);
            } else {
                $ar->where('change_type', $pageData['change_type']);
            }
        }

        if (isset($pageData['beg_time'])) {
            $ar->where('created_time', '>=', $pageData['beg_time']);
        }
        if (isset($pageData['end_time'])) {
            $ar->where('created_time', '<=', $pageData['end_time']);
        }

        $result['count'] = $ar->count('log_id');
        $result['pagecount'] = ceil(($result['count'] / $pageSize));
        $result['list'] = $ar->page($page, $pageSize)->order('created_time desc')->select()->toArray();

        return $result;
    }
}