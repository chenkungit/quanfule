<?php

namespace app\common\model;

use think\Model;

class Freeship extends Model
{

    /*商品显示  后台接口保证商品唯一性*/
    public function freeshipping_goods($gsup_id)
    {

        $now = date('Y-m-d H:i:s');

        $where = [
            ['fg.gsup_id', '=', $gsup_id],
            ['fa.beg_time', '<=', $now],
            ['fa.end_time', '>=', $now]
        ];
        $order = 'case  type
                     when 1 then number 
                     when 2 then amount 
                end
                asc';
        $res = $this->name('freeshipping_goods')->alias('fg')->join('ecs_freeshipping_activities fa', 'fg.fa_id = fa.fa_id')->where($where)->orderRaw($order)->select();

        return count($res) ? $res : false;
    }

    /*-相同包邮活动的整理归类是否满足条件-*/
    public function freeshipping_arrange($arr)
    {

        $freeshipping_subweight = 0;

        $x = [];
        foreach ($arr as $key => &$item) {
            if (isset($item['freeshipping_activity'])) {
                foreach ($item['freeshipping_activity'] as $fa_key => &$fa) {

                    $x[$fa['fa_id']]['freeshipping'] = $fa;
                    $x[$fa['fa_id']]['item'][] = $item;

//                    $x[$fa['fa_id']][$fa_key]['freeshipping_activity_type'] = $fa['type'];
//                    $x[$fa['fa_id']][$fa_key]['freeshipping_activity_number'] = $fa['number'];
//                    $x[$fa['fa_id']][$fa_key]['freeshipping_activity_amount'] = $fa['amount'];
                }
//                $x[$item['freeshipping_activity_id']][$key] = $item;
            }
        }

        if (!isset($x)) {
            return ['goods_list' => $arr, 'freeshipping_subweight' => $freeshipping_subweight];
        }
        $x = array_values($x);
        foreach ($x as $key2 => &$value2) {


            $activity_type = $value2['freeshipping']['type'];
            $activity_number = $value2['freeshipping']['number'];
            $activity_amount = $value2['freeshipping']['amount'];


            $activity_goods_number = array_sum(
                array_map(function ($val) {
                    return $val['goods_number'];
                }, $value2['item'])
            );

            $activity_goods_amount = array_sum(
                array_map(function ($val) {
                    return $val['subtotal'];
                }, $value2['item'])
            );

            foreach ($value2['item'] as &$item2) {
                if (isset($item2['is_freeshipping']) && $item2['is_freeshipping']) continue; //已包邮的break
                if ($activity_type == 1) {
                    if ($activity_goods_number >= $activity_number) {
                        $item2['is_freeshipping'] = 1;
                    } else {
                        $item2['is_freeshipping'] = 0;
                    }
                } else if ($activity_type == 2) {
                    if ($activity_goods_amount >= $activity_amount) {
                        $item2['is_freeshipping'] = 1;
                    } else {
                        $item2['is_freeshipping'] = 0;
                    }
                } else {
                    $item2['is_freeshipping'] = 0;
                }

            }


        }

        foreach ($arr as $key => &$item) {
            if (isset($item['is_freeshipping'])) {
                if ($item['is_freeshipping'] == 1) {
                    $item['freeshipping_subweight'] = $item['subweight'];
                    $freeshipping_subweight += $item['subweight'];
                } else {
                    $item['freeshipping_subweight'] = 0;
                }
            }

        }

        return ['goods_list' => $arr, 'freeshipping_subweight' => $freeshipping_subweight];
    }
}