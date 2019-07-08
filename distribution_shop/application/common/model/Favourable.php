<?php

namespace app\common\model;

use think\Model;

class Favourable extends Model
{
    /*商品促销*/
    public function fav_goods($gsup_id,$all_show=false)
    {

        $now = date('Y-m-d H:i:s');

        if($all_show){
            $where = [
                ['range','=',0],
                ['beg_time', '<=', $now],
                ['end_time', '>=', $now]
            ];
            $res = $this->name('fav_activities')->where($where)->find();

        }else{
            $where = [
                ['favg.gsup_id', '=', $gsup_id],
                ['fav.beg_time', '<=', $now],
                ['fav.end_time', '>=', $now]
            ];
            $order = 'case  type
                     when 1 then number 
                     when 2 then amount 
                end
                asc';
            $res = $this->name('fav_goods')->alias('favg')->leftJoin('ecs_fav_activities fav', 'fav.fv_id = favg.fv_id')->where($where)->orderRaw($order)->find();
        }

        if (!$res) {
            return false;
        }
//        if (strpos(',' . $res['user_rank'] . ',', ',' . $GLOBALS['user_rank'] . ',') === false) {
//            return false;
//        }
        return $res;

    }

    /*全场促销 跟商品总价总数相关*/
    public function all_fav($goods_total, $subnumber)
    {

        $now = date('Y-m-d H:i:s');

        $where = [
            ['beg_time', '<=', $now],
            ['end_time', '>=', $now],
            ['range', '=', 0]
        ];
        $arr = $this->name('fav_activities')->where($where)->select();
        /*-不存在则返回-*/
        if (count($arr) == 0) {
            return false;
        }

        foreach ($arr as &$item) {
            $discount_price = 0;
            //数量满减
            if ($item['type'] == 1) {
                if ($subnumber >= $item['number']) {
                    $repeat_sum = intval($subnumber / $item['number']);
                    if ($item['reduce_type'] == 1) {
                        if ($item['is_repeat'] == 1) {
                            $discount_price = $item['reduce_arg'] * $repeat_sum;
                        } else {
                            $discount_price = $item['reduce_arg'];
                        }
                    }
                    if ($item['reduce_type'] == 2) {
                        if ($item['is_repeat'] == 1) {
                            $discount_price = $goods_total - $item['reduce_arg'] * pow((100 - $item['reduce_arg']) / 100, $repeat_sum);
                        } else {
                            $discount_price = $goods_total - $item['reduce_arg'] * (100 - $item['reduce_arg']) / 100;
                        }
                    }
                } else {
                    $item['difference_number'] = $item['number'] - $subnumber;
                }
            }
            //价格满减
            if ($item['type'] == 2) {
                if ($goods_total >= $item['amount']) {
                    $repeat_sum = intval($goods_total / $item['amount']);

                    if ($item['reduce_type'] == 1) {
                        if ($item['is_repeat'] == 1) {
                            $discount_price = $item['reduce_arg'] * $repeat_sum;
                        } else {
                            $discount_price = $item['reduce_arg'];
                        }
                    }
                    if ($item['reduce_type'] == 2) {
                        if ($item['is_repeat'] == 1) {
                            $discount_price = $goods_total - $item['reduce_arg'] * pow((100 - $item['reduce_arg']) / 100, $repeat_sum);
                        } else {
                            $discount_price = $goods_total - $item['reduce_arg'] * (100 - $item['reduce_arg']) / 100;
                        }
                    }
                } else {
                    $item['difference_amount'] = $item['amount'] - $goods_total;
                }
            }
            $item['discount_price'] = $discount_price;
        }


        return $arr->toArray();

    }


    /*-相同优惠活动的整理归类是否满足条件-*/
    public function fav_arrange($arr)
    {

        $discount_price = 0;

        foreach ($arr as $key => $item) {
            if (isset($item['fav_activity_id'])) {
                $x[$item['fav_activity_id']][$key] = $item;
            }
        }
        if (!isset($x)) {
            return ['goods_list' => $arr, 'discount_price' => $discount_price];
        }

        foreach ($x as $key => &$item) {
            $discount_current = 0; /*-当前活动的总折扣 每次遍历初始化一次-*/
            foreach ($item as $xx) {

                $activity_type = $xx['fav_type'];
                $activity_number = $xx['fav_number'];
                $activity_amount = $xx['fav_amount'];
                $is_repeat = $xx['fav_is_repeat'];
                $reduce_type = $xx['fav_reduce_type'];
                $reduce_arg = $xx['fav_reduce_arg'];
                break;
            }
            $activity_goods_number = array_sum(
                array_map(function ($val) {
                    return $val['goods_number'];
                }, $item)
            );
            $activity_goods_amount = array_sum(
                array_map(function ($val) {
                    return $val['subtotal'];
                }, $item)
            );

            //数量满减
            if ($activity_type == 1) {
                if ($activity_goods_number >= $activity_number) {
                    $repeat_sum = intval($activity_goods_number / $activity_number);
                    if ($reduce_type == 1) {
                        if ($is_repeat == 1) {
                            $discount_current = $reduce_arg * $repeat_sum;
                        } else {
                            $discount_current = $reduce_arg;
                        }
                    }

                    if ($reduce_type == 2) {
                        if ($is_repeat == 1) {
                            $discount_current = $activity_goods_amount - $activity_goods_amount * pow((100 - $reduce_arg) / 100, $repeat_sum);
                        } else {
                            $discount_current = $activity_goods_amount - $activity_goods_amount * ((100 - $reduce_arg) / 100);
                        }
                    }

                    array_map(function (&$val) use ($activity_goods_amount, $discount_current) {
                        $val['discount'] += round($val['subtotal'] / $activity_goods_amount * $discount_current, 2);
                    }, $item);

                    $discount_price += $discount_current;
                }
                //else{
//                    $item['difference_number'] = $activity_number - $activity_goods_number;
//                }
                continue;
            }
            //价格满减
            if ($activity_type == 2) {

                if ($activity_goods_amount >= $activity_amount) {
                    $repeat_sum = intval($activity_goods_amount / $activity_amount);

                    if ($reduce_type == 1) {

                        if ($is_repeat == 1) {
                            $discount_current = $reduce_arg * $repeat_sum;
                        } else {
                            $discount_current = $reduce_arg;
                        }
                    }
                    if ($reduce_type == 2) {
                        if ($is_repeat == 1) {
                            $discount_current = $activity_goods_amount - $activity_goods_amount * pow((100 - $reduce_arg) / 100, $repeat_sum);
                        } else {
                            $discount_current = $activity_goods_amount - $activity_goods_amount * ((100 - $reduce_arg) / 100);
                        }
                    }

                    array_map(function (&$val) use ($activity_goods_amount, $discount_current) {
                        $val['discount'] += round($val['subtotal'] / $activity_goods_amount * $discount_current, 2);
                    }, $item);

                    $discount_price += $discount_current;
                }
//                else{
//                    $item['difference_amount'] = $item['amount'] - $goods_total;
//                }
                continue;
            }


        }

        return ['goods_list' => $arr, 'discount_price' => round($discount_price, 2)];
    }
}