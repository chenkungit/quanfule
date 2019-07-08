<?php
namespace app\common\model;

use think\Model;

class Activity extends Model
{

    /*商品显示  后台接口保证商品唯一性*/
    public function freeshipping_goods($gsup_id){

        $now = date('Y-m-d H:i:s');

        $where = [
            ['fg.gsup_id','=',$gsup_id],
            ['fa.beg_time','<=',$now],
            ['fa.end_time','>=',$now],
        ];

        $res = $this->name('freeshipping_goods')->alias('fg')->join('ecs_freeshipping_activities fa','fg.fa_id = fa.fa_id','LEFT')->where($where)->find();

        if(!$res){
            return false;
        }
        if($res){
            $status['freeshipping_activity_id']   = $res['fa_id'];
            $status['freeshipping_activity_name'] = $res['activity_name'];
            $status['freeshipping_type'] = $res['type'];
            $status['freeshipping_amount'] = $res['amount'];
            $status['freeshipping_number'] = $res['number'];

            return $status;
        }
    }


    /*商品促销*/
    public function fav_goods($gsup_id){
        $now = date('Y-m-d H:i:s');

        $where = [
            ['favg.gsup_id','=',$gsup_id],
            ['fav.beg_time','<=',$now],
            ['fav.end_time','>=',$now],
        ];

        $res = $this->name('fav_goods')->alias('favg')->join('ecs_fav_activities fav','fav.fv_id = favg.fv_id','LEFT')->where($where)->find();

        if(!$res){
            return false;
        }
        if (strpos(',' . $res['user_rank'] . ',', ',' . $GLOBALS['user_rank'] . ',') === false) {
            return false;
        }
        return $res;

    }


    /*-相同包邮活动的整理归类是否满足条件-*/
    public function freeshipping_arrange($arr){

        $freeshipping_subweight = 0;

        foreach ($arr as $key=>$item){
            if(isset($item['freeshipping_activity_id'])){
                $x[$item['freeshipping_activity_id']][$key] = $item;
            }
        }
        if(!isset($x)){
            return ['goods_list'=>$arr,'freeshipping_subweight'=>$freeshipping_subweight];
        }
        foreach ($x as $key=>&$item){

            foreach ($item as $xx){
                $activity_type =$xx['freeshipping_activity_type'];
                $activity_number =$xx['freeshipping_activity_number'];
                $activity_amount =$xx['freeshipping_activity_amount'];
                break;
            }
            $activity_goods_number = array_sum(
                array_map(function ($val){
                    return $val['goods_number'];
                },$item)
            );
            $activity_goods_amount = array_sum(
                array_map(function ($val){
                    return $val['goods_total'];
                },$item)
            );

            if($activity_type == 1){
                if($activity_goods_number>=$activity_number){
                    $ac[$key]['is_freeshipping'] = 1;
                }else{
                    $ac[$key]['is_freeshipping'] =0;
                }
            }else if($activity_type == 2){
                if($activity_goods_amount>=$activity_amount){
                    $ac[$key]['is_freeshipping'] = 1;
                }else{
                    $ac[$key]['is_freeshipping'] = 0;
                }
            }else{
                $ac[$key]['is_freeshipping'] = 0;
            }
        }


        foreach ($arr as $key => &$item){
            foreach ($ac as $key2=>$item2) {

                if(isset($item['freeshipping_activity_id']) && $item['freeshipping_activity_id'] == $key2){
                    $item['is_freeshipping'] = $item2['is_freeshipping'];

                    if($item['is_freeshipping']==1){
                        $item['freeshipping_subweight'] = $item['subweight'];
                        $freeshipping_subweight += $item['subweight'];
                    }else{
                        $item['freeshipping_subweight'] = 0;
                    }
                }
            }
        }

        return ['goods_list'=>$arr,'freeshipping_subweight'=>$freeshipping_subweight];
    }



    /*全场促销 跟商品总价相关*/
    public function all_fav($goods_total,$subnumber){

        $now = date('Y-m-d H:i:s');

        $where = [
            'beg_time'=>['<=',$now],
            'end_time'=>['>=',$now],
            'range'=>0
        ];
        $arr = $this->name('fav_activities')->where($where)->select();
        /*-不存在则返回-*/
        if(!$arr){
            return false;
        }

        /*-将不符合用户等级的过滤掉-*/
        foreach ($arr as $key=> &$item){

            if (strpos(',' . $item['user_rank'] . ',', ',' . $GLOBALS['user_rank'] . ',') === false) {

                unset($arr[$key]);
            }else{

            }
        }

        if($arr){
            foreach ($arr as &$item){
                $discount_price = 0;
                //数量满减
                if($item['type'] == 1){
                    if($subnumber >= $item['number']){
                        $repeat_sum= intval($subnumber/$item['number']);
                        if($item['reduce_type'] == 1){
                            if($item['is_repeat'] == 1){
                                $discount_price = $item['reduce_arg']*$repeat_sum;
                            }else{
                                $discount_price = $item['reduce_arg'];
                            }
                        }
                        if($item['reduce_type'] == 2){
                            if($item['is_repeat'] == 1){
                                $discount_price =  $goods_total- $item['reduce_arg']*pow((100-$item['reduce_arg'])/100,$repeat_sum);
                            }else{
                                $discount_price =  $goods_total- $item['reduce_arg']*(100-$item['reduce_arg'])/100;
                            }
                        }
                    }else{
                        $item['difference_number'] = $item['number'] - $subnumber;
                    }
                }
                //价格满减
                if($item['type'] == 2){
                    if($goods_total >= $item['amount']){
                        $repeat_sum= intval($goods_total/$item['amount']);

                        if($item['reduce_type'] == 1){
                            if($item['is_repeat'] == 1){
                                $discount_price = $item['reduce_arg']*$repeat_sum;
                            }else{
                                $discount_price = $item['reduce_arg'];
                            }
                        }
                        if($item['reduce_type'] == 2){
                            if($item['is_repeat'] == 1){
                                $discount_price =  $goods_total- $item['reduce_arg']*pow((100-$item['reduce_arg'])/100,$repeat_sum);
                            }else{
                                $discount_price =  $goods_total- $item['reduce_arg']*(100-$item['reduce_arg'])/100;
                            }
                        }
                    }else{
                        $item['difference_amount'] = $item['amount'] - $goods_total;
                    }
                }
                $item['discount_price'] = $discount_price;
            }

            return $arr;
        }
    }

    /*-相同优惠活动的整理归类是否满足条件-*/
    public function fav_arrange($arr){
        $discount_price = 0;

        foreach ($arr as $key=>$item){
            if(isset($item['fav_activity_id'])){
                $x[$item['fav_activity_id']][$key] = $item;
            }
        }
        if(!isset($x)){
            return ['goods_list'=>$arr,'discount_price'=>$discount_price];
        }

        foreach ($x as $key=>&$item){

            foreach ($item as $xx){

                $activity_type =$xx['fav_type'];
                $activity_number =$xx['fav_number'];
                $activity_amount =$xx['fav_amount'];
                $is_repeat = $xx['fav_is_repeat'];
                $reduce_type = $xx['fav_reduce_type'];
                $reduce_arg = $xx['fav_reduce_arg'];
                break;
            }
            $activity_goods_number = array_sum(
                array_map(function ($val){
                    return $val['goods_number'];
                },$item)
            );
            $activity_goods_amount = array_sum(
                array_map(function ($val){
                    return $val['goods_total'];
                },$item)
            );

            $discount_price = 0;
            //数量满减
            if($activity_type == 1){
                if($activity_goods_number >= $activity_number){
                    $repeat_sum= intval($activity_goods_number/$activity_number);
                    if($reduce_type == 1){
                        if($is_repeat == 1){
                            $discount_price += $reduce_arg*$repeat_sum;
                        }else{
                            $discount_price += $reduce_arg;
                        }
                    }
                    if($activity_type == 2){
                        if($is_repeat == 1){
                            $discount_price +=  $activity_goods_amount- $reduce_arg*pow((100-$item['reduce_arg'])/100,$repeat_sum);
                        }else{
                            $discount_price +=  $activity_goods_amount- $reduce_arg*(100-$item['reduce_arg'])/100;
                        }
                    }
                }//else{
//                    $item['difference_number'] = $activity_number - $activity_goods_number;
//                }
                continue;
            }
            //价格满减
            if($activity_type == 2){
                if($activity_goods_amount >= $activity_amount){
                    $repeat_sum= intval($activity_goods_amount/$activity_amount);

                    if($reduce_type == 1){
                        if($is_repeat == 1){
                            $discount_price += $reduce_arg*$repeat_sum;
                        }else{
                            $discount_price += $reduce_arg;
                        }
                    }
                    if($reduce_type == 2){
                        if($is_repeat == 1){
                            $discount_price +=  $activity_goods_amount - $reduce_arg*pow((100-$reduce_arg)/100,$repeat_sum);
                        }else{
                            $discount_price +=  $activity_goods_amount - $reduce_arg*(100-$reduce_arg)/100;
                        }
                    }
                }
//                else{
//                    $item['difference_amount'] = $item['amount'] - $goods_total;
//                }
                continue;
            }

        }


        return ['goods_list'=>$arr,'discount_price'=>$discount_price];
    }








}