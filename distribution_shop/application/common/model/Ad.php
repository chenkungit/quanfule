<?php

namespace app\common\model;
use think\Model;

class Ad extends model
{

    const qinniu_url = '';
    public function theme_show($type){
        $goods = new Goods();

        $where = [
            ['enabled','=',1],
            ['carousel_type','=',$type]
        ];
        $res = $this->name('index_theme')->field('theme_name,theme_banner,redirect_type,redirect_id,children_goods_id,'.concat_img('theme_banner','',self::qinniu_url))->where($where)->order('sort asc')->select();

        foreach ($res as $key1=>$val1) {
            $g_arr=[];
            $children_goods_id = explode(',',trim($val1['children_goods_id']));

            if($children_goods_id){
                $good_info=$goods->get_goods_content($children_goods_id);
                if($good_info)
                {
                    array_push($g_arr,$good_info);
                }
            }
            $res[$key1]['children_goods']=$g_arr;
        }

        return $res;
    }

    public function freeshipping_show(int $fa_id,int $page = 1,int $limit = 10){

        $goods = new Goods();
        $g_arr = [];
        $gsup_ids =  $this->name('freeshipping_goods')->alias('fg')
                    ->leftJoin('goods_sup sup','fg.gsup_id = sup.id')
                    ->page($page,$limit)->where('fa_id',$fa_id)->order('sup.ord desc')->where('sup.is_sale','in',[0,2])->column('gsup_id');
        if($gsup_ids){
            $good_info=$goods->get_goods_content($gsup_ids);
            if($good_info)
            {
                $g_arr = array_merge($g_arr,$good_info);
            }
        }

        return $g_arr;
    }
}