<?php
namespace app\dashboard\model;

use think\Model;
class Attribute  extends Model
{

        protected $table = 'ecs_attribute';

        protected $arr = [
            'item' => [],
            'pagecount'=>1,
        ];

        //获取数据代码
        public function get_Attributelist($page,$limit,$goods_type,$sort_by,$sort_order)
        {
            if($goods_type == 0){

                $res =$this->table('ecs_attribute')->alias('a')->join('goods_type t ','a.cat_id=t.cat_id','         
        LEFT')->field('a.*,t.cat_id,t.cat_name')->page("$page,$limit")->order($sort_by ,$sort_order)->select();

                $count =$this->table('ecs_attribute')->alias('a')->join('goods_type t ','a.cat_id=t.cat_id','         
        LEFT')->field('a.*,t.cat_id,t.cat_name')->order($sort_by ,$sort_order)->count();

            } else {

                $res = $this->table('ecs_attribute')->alias('a')->join('goods_type t ', 'a.cat_id=t.cat_id', ' 
        LEFT')->field('a.*,t.cat_id,t.cat_name')->where('a.cat_id', $goods_type)->page("$page,$limit")->order($sort_by, $sort_order)->select();
               
                $count = $this->table('ecs_attribute')->alias('a')->join('goods_type t ', 'a.cat_id=t.cat_id', ' 
        LEFT')->field('a.*,t.cat_id,t.cat_name')->where('a.cat_id', $goods_type)->order($sort_by, $sort_order)->count();

            }

            if($res){
//
//                foreach ($res AS $key => $val)
//                {
//                    $res[$key]['attr_values']   = str_replace("\n", ", ", $val['attr_values']);
//                }

                $this->arr['item'] = $res;
                $this->arr['pagecount'] = ceil($count/$limit);

            }

            return $this->arr;
        }


}