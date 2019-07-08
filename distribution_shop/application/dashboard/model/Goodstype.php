<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2017/12/4
 * Time: 8:49
 */

namespace app\dashboard\model;

use think\Model;

class Goodstype extends Model
{
    protected $table = 'ecs_goods_type';

    protected $arr = [
        'item' => [],
        'pagecount'=>1,
    ];

//获取数据代码
    public function get_Goodstypelist($page,$limit)
    {

        $res = $this->table('ecs_goods_type')->alias('t')->join('ecs_attribute a ', 'a.cat_id=t.cat_id', 'LEFT')->field('t.*,COUNT(a.cat_id) AS attr_count')->page("$page,$limit")->group('t.cat_id')->select();

        if($res){
//            foreach ($res AS $key=>$val)
//            {
//                $res[$key]['attr_group'] = strtr($val['attr_group'], array("\r" => '', "\n" => ", "));
//            }
            $this->arr['item'] = $res;
            $this->arr['pagecount'] = ceil($this->count()/$limit);
        }

        return $this->arr;
    }
}