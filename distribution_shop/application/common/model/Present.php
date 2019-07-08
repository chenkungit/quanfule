<?php
namespace app\common\model;

use think\Model;

class Present extends Model
{
    /*全场促销 跟商品总价总数相关*/
    public function all_pre($goods_total,$subnumber){

        $now = date('Y-m-d H:i:s');
        $present = [];
        $number_present_info = [];
        $amount_present_info = [];
        $where = [
            ['beg_time','<=',$now],
            ['end_time','>=',$now],
            ['range','=',0]
        ];
        $order = 'case  type
                     when 1 then number 
                     when 2 then amount 
                end
                asc';
        $arr = $this->name('present_activities')->where($where)->orderRaw($order)->select()->toArray();


        /*-不存在则返回-*/
        if(!$arr){
            return false;
        }

        /*-将不符合用户等级的过滤掉-*/
//        foreach ($arr as $key=> &$item){
//
//            if (strpos(',' . $item['user_rank'] . ',', ',' . $GLOBALS['user_rank'] . ',') === false) {
//                unset($arr[$key]);
//            }
//        }
        if(!$arr){
            return false;
        }

        foreach ($arr as &$item){
            $number = 0;
            //数量满减
            if($item['type'] == 1){
                if($subnumber >= $item['number']){
                    $repeat_sum= intval($subnumber/$item['number']);
                    if($item['is_repeat'] == 1){
                        $number = $repeat_sum;
                    }else{
                        $number = 1;
                    }
                    $number_present_info = $this->present_info($item['present_sup_id'],$number);
                }else{
                    $item['difference_number'] = $item['number'] - $subnumber;
                }
            }
            //价格满减
            if($item['type'] == 2){
                if($goods_total >= $item['amount']){
                    $repeat_sum= intval($goods_total/$item['amount']);

                    if($item['is_repeat'] == 1){
                        $number = $repeat_sum;
                    }else{
                        $number = 1;
                    }
                    $amount_present_info = $this->present_info($item['present_sup_id'],$number);
                }else{
                    $item['difference_amount'] = $item['amount'] - $goods_total;
                }
            }
        }

        $present = array_merge([$amount_present_info],[$number_present_info]);
        array_walk($present,function (&$value,$key) use(&$present){
            if(!count($value)){
                unset($present[$key]);
            }
        });
        return ['item'=>$arr,'present'=>$present];
    }


    public function pre_goods($gsup_id){

        $now = date('Y-m-d H:i:s');

        $where = [
            ['pg.gsup_id','=',$gsup_id],
            ['pa.beg_time','<=',$now],
            ['pa.end_time','>=',$now]
        ];
        $order = 'case  type
                     when 1 then number 
                     when 2 then amount 
                end
                asc';
        $res = $this->name('present_goods')->alias('pg')->join('ecs_present_activities pa','pg.pa_id = pa.pa_id','LEFT')->orderRaw($order)->where($where)->find();

        if(!$res){
            return false;
        }
        return $res;
    }


    /*-相同优惠活动的整理归类是否满足条件-*/
    public function pre_arrange($arr){
        $number = 0;
        $present = [];
        foreach ($arr as $key=>$item){
            if(isset($item['pre_activity_id'])){
                $x[$item['pre_activity_id']][$key] = $item;
            }
        }

        if(!isset($x)){
            return ['goods_list'=>$arr,'present'=>[]];
        }

        foreach ($x as $key=>&$item){

            foreach ($item as $xx){

                $activity_type = $xx['pre_type'];
                $activity_number = $xx['pre_number'];
                $activity_amount = $xx['pre_amount'];
                $is_repeat = $xx['pre_is_repeat'];
                $present_sup_id = $xx['pre_present_sup_id'];
                break;
            }
            $activity_goods_number = array_sum(
                array_map(function ($val){
                    return $val['goods_number'];
                },$item)
            );
            $activity_goods_amount = array_sum(
                array_map(function ($val){
                    return $val['subtotal'];
                },$item)
            );

            $number = 0;
            //数量满减
            if($activity_type == 1){
                if($activity_goods_number >= $activity_number){
                    $repeat_sum= intval($activity_goods_number/$activity_number);
                    if($is_repeat == 1){
                        $number = $repeat_sum;
                    }else{
                        $number = 1;
                    }
                }
                goto numberjudge;
            }
            //价格满减
            if($activity_type == 2){
                if($activity_goods_amount >= $activity_amount){
                    $repeat_sum= intval($activity_goods_amount/$activity_amount);
                    if($is_repeat == 1){
                        $number = $repeat_sum;
                    }else{
                        $number = 1;
                    }
                }
                goto numberjudge;
            }

            numberjudge:
            if($number){
                $present_info = $this->present_info($present_sup_id,$number);
                $present[] = $present_info;
            }

        }



        return ['goods_list'=>$arr,'present'=>$present];
    }



    protected function present_info($gsup_id,$number){

        $fields = 'shp.id,shp.goods_id,shp.shops_id,sup.supplier,price as market_price,if(vs_id != 0, price, price * ' . $GLOBALS['discount'] . ') AS goods_price,sup.name,sup.code,'.concat_img('thumbnail');

        $res = $this->name('goods_sup')->alias('sup')->field($fields)->where('sup.id','=',$gsup_id)->leftJoin('ecs_goods_shp shp','sup.id=shp.goods_id')->find()->toArray();


        $res['name'] = $res['name']."(赠品)";
        $res['goods_price']  = (string)0;
        $res['goods_number'] = $number;

        return $res;
    }

}