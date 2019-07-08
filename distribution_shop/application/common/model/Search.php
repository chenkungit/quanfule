<?php

namespace app\common\model;

use GuzzleHttp\Client;


class Search extends Load
{

    public function getSearchGoods($key_arr, $presale, $sort_order, $page, $limit, $beon)
    {


        $beon_order = $sort_order;


        if ($beon == 5) {
            $where = 'sup.is_crowdfunding =0 and sup.is_delete = 0 AND sup.is_sale in (0,2,3)';
        } else {
            $where = 'sup.is_crowdfunding =0 and sup.is_delete = 0 AND sup.is_sale in (0,2)';
        }

        foreach ($key_arr as $item) {
            $where .= " AND se.search LIKE '$item'";
        }

        $discount = $this->get_discount();
        $join = [
            ['goods_sup sup', 'shp.goods_id = sup.id', 'LEFT'],
            ['goods_search se', 'se.good_id = sup.id', 'LEFT'],
        ];
        $fields = 'shp.id,shp.goods_id,shp.shops_id,sup.name,sup.stock,sup.price_temp,sup.img as thumbnail,sup.img,sup.type,sup.icon,shp.base_sale as sales,sup.add_time as add_time,sup.price as market_price,sup.is_sale,sup.is_fei5zhe,sup.marketing_type ';
        $fields .= ",sup.price *" . $discount . " AS sort_price";
        $arr = $this->name('goods_shp')->alias('shp')->field($fields)->join($join)->where($where)->order($beon_order)->group('sup.id')->page("$page,$limit")->select();
        foreach ($arr as $key => &$value) {

            $price = $this->get_goods_final_price($value['goods_id'], $value['sort_price'], $value['market_price'], [], 0, 0, $value['is_fei5zhe']);
            $value['price'] = $this->price_format($price);

            $value['market_price'] = $this->price_format($value['market_price']);

            $value['presale'] = 0;
            if (count($this->name('products')->where('goods_id', $value['goods_id'])->select()) > 0) {
                $value['stock'] = $this->name('products')->where('goods_id', $value['goods_id'])->sum('stock');
            }


//            $activities_list = (new Goods())->activities_list($value['goods_id']);
//            $watermark = $activities_list['watermark'];
            $value['thumbnail'] = build_img_uri($value['thumbnail'], $arr[$key]['stock'], '', $value['is_sale']);
            $value['img'] = build_img_uri($value['img'], $arr[$key]['stock'], '', $value['is_sale']);

            $value['is_dingjin'] = 0;
            $value['mark'] = mark($value['is_sale'], $value['presale'], $value['is_dingjin'], $value['stock']);
            $value['after_mark'] = [];
        }
        return $arr;
    }


    //对关键词进行分词
    public static function scws_keywords($keyword)
    {
//        $dict_url = App::getRootPath().'data/dict_extra.txt';

//        echo $dict_url;
        $so = scws_new();

        // 这里没有调用 set_dict 和 set_rule 系统会自动试调用 ini 中指定路径下的词典和规则文件
        $so->set_charset('utf8');

        $so->set_dict('/usr/local/scws/etc/dict.utf8.xdb');

//        $so->add_dict($dict_url, SCWS_XDICT_TXT);  //添加自定义分词库

        $so->send_text($keyword);
        $ret = array();
        while ($tmp = $so->get_result()) {

            $ret[] = $tmp;
        }
        $so->close();


        $word_arr = array_column($ret[0], 'word');
        return array_map(function ($item) {
            return '%' . $item . '%';
        }, $word_arr);
    }

    public function scws_keywords_api($keyword)
    {

        $client = new Client();

        $response = $client->request('POST', 'http://www.xunsearch.com/scws/api.php', [
            'form_params' => [
                'data' => $keyword,
                'multi' => 0,
                'respond' => 'json',  //能调这个接口的 逻辑上肯定是有登陆态了
            ]
        ]);
        $res = json_decode($response->getBody(), true);

        $word_arr = array_column($res['words'], 'word');
        return array_map(function ($item) {
            return '%' . $item . '%';
        }, $word_arr);
    }

}