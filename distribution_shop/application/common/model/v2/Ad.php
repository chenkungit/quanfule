<?php

namespace app\common\model\v2;

use think\Model;
use app\common\model\Goods;

class Ad extends model
{

    const qinniu_url = '';

    public function theme_show($type, $page, $limit,$v=2)
    {
        $goods = new Goods();

        if($v==2){
            $where = [
                ['carousel_type', '=', $type],
                ['enabled', '=', 1],
                ['sort', '<', 100],
            ];
        }else{
            $where = [
                ['carousel_type', '=', $type],
                ['enabled', '=', 1],
                ['sort', '>=', 100],
            ];
        }

        $res = $this->name('index_theme')->field('sort,theme_name,theme_banner,redirect_type,redirect_id,children_goods_id,' . concat_img('theme_banner', '', self::qinniu_url))
            ->where($where)->page("$page,$limit")->order('sort asc')->select();

        foreach ($res as $key1 => $val1) {
            $g_arr = [];
            $children_goods_id = array_filter(explode(',', trim($val1['children_goods_id'])));
//            var_dump($val1['children_goods_id'],$children_goods_id);
//            $children_goods_id[] = "";
//            var_dump($children_goods_id);
//            var_dump(array_filter($children_goods_id));
//            exit;


            if ($children_goods_id) {
                $good_info = $goods->get_goods_content($children_goods_id);

                if ($good_info) {
                    $g_arr = array_merge($g_arr, $good_info);
                }
            }
            $res[$key1]['children_goods'] = $g_arr;

        }

        return $res;
    }
}