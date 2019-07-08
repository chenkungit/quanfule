<?php
namespace app\common\model;

use think\Model;


class Water extends Model
{
    public function water_goods($gsup_id,$all_show=false)
    {

        $now = date('Y-m-d H:i:s');

        if($all_show){
            $where = [
                ['beg_time', '<=', $now],
                ['end_time', '>=', $now]
            ];
            $res = $this->name('water_activities')->where($where)->find();

        }else{
            $where = [
                ['favg.gsup_id', '=', $gsup_id],
                ['fav.beg_time', '<=', $now],
                ['fav.end_time', '>=', $now]
            ];
//            $order = 'case  type
//                     when 1 then number
//                     when 2 then amount
//                end
//                asc';
            $res = $this->name('water_goods')->alias('favg')->leftJoin('ecs_water_activities fav', 'fav.fw_id = favg.fw_id')->where($where)->find();
        }

        if (!$res) {
            return false;
        }
//        if (strpos(',' . $res['user_rank'] . ',', ',' . $GLOBALS['user_rank'] . ',') === false) {
//            return false;
//        }
        return $res;

    }
}