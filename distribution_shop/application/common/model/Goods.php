<?php

namespace app\common\model;

use app\common\Utils\PriceHelper;
use code;
use think\Db;
use think\db\Query;

class Goods extends Load
{

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function get_goods_info($gshp_id, $code, $gsup_id, $is_group=0)
    {


        $where = [
            ['sup.is_delete', '=', 0],
//            'sup.is_sale'=>0,
//            'shp.status'=>0,
        ];
        /*-门店扫码获取k3码-*/
        if ($code) {
            array_push($where, ['sup.code', '=', $code]);
        }
        /*-线上商品id-*/
        if ($gshp_id) {
            array_push($where, ['shp.id', '=', $gshp_id]);
        }
        if ($gsup_id) {
            array_push($where, ['sup.id', '=', $gsup_id]);
        }


        /*sup.img,shp.new,shp.hot,shp.fine,sup.code,,shp.hits,shp.is_freeshipping,sup.icon*/
        $field = 'shp.id as gshp_id,shp.suppliers_id,shp.shops_id,sup.stock,sup.start_num,sup.Nbei,send.address as sendAddress,send.id as sendAddress_id,sup.asked_question';
        $field .= ',sup.album,sup.id as gsup_id,sup.name,sup.package,sup.brand,sup.price_temp,sup.img,sup.weight,sup.video_url,sup.video_img,sup.is_you,sup.is_sale,sup.descpt,sup.m_descpt,sup.type,sup.add_time,sup.use_points,sup.start_num,sup.Nbei,sup.category,';
        $field .= 'sup.app_rate,sup.vs_id,sup.is_miao,sup.tbsm,sup.is_virtual,sup.is_crowdfunding,sup.is_dingjin,sup.is_yuding,sup.rel_articles_id,sup.rel_goods_id,sup.youhui_type,sup.youhui_desc,sup.tishi_desc,sup.shipping_desc,sup.price as market_price';
        $field .= ',cat.measure_unit, cat.cat_name, b.brand_name,shops.name as shops_name,sup.marketing_type';
        $field .= ',if(vs_id != 0, sup.price, sup.price * ' . $GLOBALS['discount'] . ') AS u_price';

        $arr = $this->name('goods_shp')->alias('shp')->field($field)
            ->leftJoin('ecs_shops shops', 'shp.shops_id = shops.id')
            ->leftJoin('ecs_goods_sup sup', 'shp.goods_id = sup.id')
            ->leftJoin('ecs_category cat', 'cat.cat_id = sup.category')
            ->leftJoin('ecs_brand b', 'b.brand_id = sup.brand')
            ->leftJoin('ecs_suppliers supr', 'supr.id = shp.suppliers_id')
            ->leftJoin('ecs_sendaddress send', 'supr.sendaddress=send.id')
            ->where($where)->find();

        if (!$arr['gshp_id']) {
            abort(404, '该商品不存在或已下架');
        }

        $activities_list = $this->activities_list($arr['gsup_id']);
        $arr['activities_list'] = $activities_list['list'];
        $arr['after_mark'] = $activities_list['after_mark'];
        $watermark = $activities_list['watermark'];

        $arr['dj'] = ['dj_status' => 0, 'price' => "0"];
        $arr['group'] = ['group_status' => 0, 'price' => 0, 'is_new' => 0];
        $marketing_price = 0;


        $price = $this->get_goods_final_price($arr['gsup_id'], $arr['u_price'], $arr['market_price'], [], $arr['app_rate'], $marketing_price, $arr['vs_id']);


        $arr['price'] = PriceHelper::format($price);

        //            $arr['price1'] = $price;
        $arr['market_price'] = PriceHelper::format($arr['market_price']);

        $arr['album'] = $activities_list['album'];
        //            $arr['app_price'] = $this->get_goods_final_price($arr['gsup_id'],$arr['u_price'],0,[],[],1);//获取app价格

        /* 用户评论级别取整 */
        $arr['comment_rank'] = 0;
        //获取商品的价格范围

        $arr['comment'] = $this->comment_info($gshp_id);

        $arr['comment_count'] = $this->name('comment')->where('id_value', $gshp_id)->count();

        $range_price = $this->get_range_price($arr['gsup_id'], $arr['u_price'], $marketing_price);
        $arr['range_price'] = $range_price;

//            $article = new Article();
        $arr['rel_articles_list'] = [];


        //获取预售信息
        $presale_list = [];

        $arr['thumbnail'] = $arr['img'];

        $arr['pictures'] = $this->get_goods_gallery($arr['gsup_id'], $arr['stock'], $watermark, $arr['is_sale']);

        $arr['presale'] = $presale_list;
        $arr['collect_count'] = $this->collect_count($gshp_id);

        return $arr;
    }

    public function get_goods_properties($gshp_id, $code, $gsup_id)
    {

        /*-门店扫码获取k3码-*/
        if ($code) {
            $where[] = ['sup.code', '=', $code];
        }
        /*-线上商品id-*/
        if ($gshp_id) {
            $where[] = ['shp.id', '=', $gshp_id];
        }
        if ($gsup_id) {
            $where[] = ['sup.id', '=', $gsup_id];
        }

        $join = [
            ['ecs_goods_sup sup', 'gt.cat_id = sup.type', 'LEFT'],
            ['ecs_goods_shp shp', 'shp.goods_id = sup.id', 'LEFT'],
        ];
        $field = 'sup.id as gsup_id,gt.attr_group';
        /* 对属性进行重新排序和分组 */
        $info = $this->name('goods_type')->alias('gt')->field($field)->join($join)->where($where)->find();

//        $grp = $info['attr_group'];
        $gsup_id = $info['gsup_id'];
//        if (!empty($grp))
//        {
//            $groups = explode("\n", strtr($grp, "\r", ''));
//        }

        $sql = "SELECT a.attr_name,a.attr_type,a.attr_group,a.attr_id,a.is_linked, " .
            "g.goods_attr_id, g.attr_value,g.img_id,gg.thumb_url " .
            'FROM ecs_goods_attr AS g ' .

            'LEFT JOIN ecs_attribute AS a ON a.attr_id = g.attr_id ' .
            'LEFT JOIN ecs_goods_gallery AS gg ON g.img_id = gg.img_id ' .
            "WHERE g.goods_id = $gsup_id " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';
        $res = $this->query($sql);

        foreach ($res as $key => $value) {
            $res[$key]['thumb_url'] = isset($res[$key]['thumb_url']) ? build_img_uri($res[$key]['thumb_url']) : '';
        }

        $arr['pro'] = array();     // 属性
        $arr['spe'] = array();     // 规格
        $arr['lnk'] = array();     // 关联的属性

        foreach ($res AS $key => $value) {
            if ($value['attr_type'] == 0 || $value['attr_type'] == 3) {
                $arr['pro'][$key]['attr_name'] = $value['attr_name'];
                $arr['pro'][$key]['attr_value'] = $value['attr_value'];
            } else {

                $arr['spe'][$value['attr_id']]['attr_type'] = $value['attr_type'];
                $arr['spe'][$value['attr_id']]['name'] = $value['attr_name'];
                $arr['spe'][$value['attr_id']]['values'][] = [
                    'label' => $value['attr_value'],
                    'thumb_url' => isset($value['thumb_url']) ? $value['thumb_url'] : '',
                    'goods_attr_id' => $value['goods_attr_id']
                ];
            }
        }

        return $arr;
    }

    public function activities_list($gsup_id)
    {
        $arr = [
            'list' => [],
            'watermark' => '',
            'after_mark' => null
        ];

        $favourable = new Favourable();
        $freeShip = new Freeship();
        $present = new Present();

        $tmp = $favourable->fav_goods($gsup_id);
        if ($tmp) {
            if ($tmp['reduce_type'] == 1) {
                $type_name = '满减';
            } else {
                $type_name = '折扣';
            }
            $arr['list'][] = [
                'type' => $type_name,
                'name' => $tmp['activity_name'],
            ];
            $arr['after_mark'][] = $type_name;
        }
        $tmp_all_fav = $favourable->fav_goods($gsup_id, true);
        if ($tmp_all_fav) {
            if ($tmp_all_fav['watermark']) {
                $tmp['watermark'] = $tmp_all_fav['watermark'];
            }
            if ($tmp_all_fav['album']) {
                $tmp['album'] = $tmp_all_fav['album'];
            }
            if ($tmp_all_fav['reduce_type'] == 1) {
                $type_name = '满减';
            } else {
                $type_name = '折扣';
            }
            $arr['list'][] = [
                'type' => $type_name,
                'name' => $tmp_all_fav['activity_name'],
            ];
            $arr['after_mark'][] = $type_name;
        }


        $tmp3 = $present->pre_goods($gsup_id);
        if ($tmp3) {
            $arr['list'][] = [
                'type' => '赠品',
                'name' => $tmp3['activity_name'],
                'watermark' => ''
            ];
        }
        $all_water_mark = '';
        $all_album = null;
        /*-将活动变成商品的参数最后在操作-*/
        $tmp2 = $freeShip->freeshipping_goods($gsup_id);
        $free_watermark = null;
        $free_album = null;
        if ($tmp2) {
            $arr1=array();
            $arr2=array();
            $arr3=array();
            foreach ($tmp2 as $val){
                if ($val['type'] == 1) {
                    $arr1[]=$val;
                } else {
                    $arr2[]=$val;
                }
            }
            if(count($arr1)){
                array_push($arr3,$arr1[0]);
            }else if(count($arr2)){
                array_push($arr3,$arr2[0]);
            }
            $tmp2=$arr3;
            foreach ($tmp2 as $item2) {
                $arr['list'][] = [
                    'type' => '包邮',
                    'name' => $item2['activity_name'],
                ];
                if ($item2['type'] == 1) {
                    $arr['after_mark1'][] = $item2['number'] == 1 ? '单件包邮' : $item2['number'] . '件包邮';
                    $arr['after_mark_type'][] = 1;
                } else {
                    $arr['after_mark1'][] = intval($item2['amount']) . '包邮';
                    $arr['after_mark_type'][] = 2;
                }
                if (!$free_watermark) {
                    $free_watermark = $item2['watermark'];
                }
                if (!$free_album) {
                    $free_album = $item2['album'];
                }
            }
        }

        //临时这么处理一下
        if(array_key_exists('after_mark1',$arr)){
            if (is_array($arr['after_mark1'])) {
                $arr['after_mark1'] = array_unique($arr['after_mark1']);
                $arr['after_mark_type'] = array_unique($arr['after_mark_type']);
                $a = array_search('1', $arr['after_mark_type']);

                if (is_numeric($a)){
                    $arr['after_mark1'] = [$arr['after_mark1'][$a]];
                }

                unset($arr['after_mark_type']);
                if(!is_array($arr['after_mark'])){
                    $arr['after_mark'] = array();
                }
                if(!is_array($arr['after_mark1'])){
                    $arr['after_mark1'] = array();
                }
                $arr['after_mark'] = array_unique(array_merge($arr['after_mark'],$arr['after_mark1']));
            }
        }

        if(is_array($arr['after_mark']))
            $arr['after_mark'] = array_values($arr['after_mark']);

        $arr['watermark'] = isset($tmp['watermark']) ? $tmp['watermark'] : ($free_watermark ? $free_watermark : $all_water_mark);
        $arr['album'] = isset($tmp['album']) ? $tmp['album'] : ($free_album ? $free_album : $all_album);

        return $arr;
    }

    public function comment_info($gshp_id)
    {

        $comment_res = $this->name('comment')->alias('c')->field("FROM_UNIXTIME(add_time,'%Y.%m.%d') AS add_time,u.user_name,u.nick,u.avatar,goods_attr,content,imgs,comment_rank")
            ->join('ecs_users u', 'u.user_id = c.user_id')
            ->where('id_value', $gshp_id)->order("add_time desc")->find();

        if (!$comment_res) return null;
        return $arr = [
            'nick' => $comment_res['nick'] ? $comment_res['nick'] : $comment_res['user_name'],
            'avatar' => $comment_res['avatar'] ? $comment_res['avatar'] : null,
            'add_time' => $comment_res['add_time'],
            'comment_rank' => $comment_res['comment_rank'],
            'content' => $comment_res['content'] ? $comment_res['content'] : null,
            'goods_attr' => $comment_res['goods_attr'] ? str_replace("\n", " ", $comment_res['goods_attr']) : null,
            'imgs' => $comment_res['imgs'] ? array_map(function ($val) {
                return trim($val);
            }, explode(',', $comment_res['imgs'])) : null
        ];
    }

    /*-
    @params stock 添加水印用的参数
    -*/
    public function get_goods_gallery($goods_id, $stock, $watermark, $is_sale)
    {
        $fields = "img_id, img_url, thumb_url, img_desc";
        $row = $this->name('goods_gallery')->field($fields)->where('goods_id', $goods_id)->where('is_show', 1)->order('sort asc,img_id desc')->limit(5)->select();

        /* 格式化相册图片路径 */
        foreach ($row as $key => $gallery_img) {
            if($key==0){
                $row[$key]['img_url'] = build_img_uri($gallery_img['img_url'], $stock, $watermark, $is_sale);
                $row[$key]['thumb_url'] = build_img_uri($gallery_img['thumb_url'], $stock, $watermark, $is_sale);
            }else{
                $row[$key]['img_url'] = build_img_uri($gallery_img['img_url'],$stock = true, $watermark = '', $is_sale = 0);
                $row[$key]['thumb_url'] = build_img_uri($gallery_img['thumb_url'],$stock = true, $watermark = '', $is_sale = 0);
            }

        }
        return $row;
    }

    public function main_goods_info($gshp_id)
    {

        $field = 'shp.id as gshp_id,shp.is_sale as shp_sale,sup.stock,shp.is_freeshipping,shp.shops_id, shp.suppliers_id,';
        $field .= 'sup.id as gsup_id,sup.code,sup.start_num,sup.Nbei,sup.name,sup.thumbnail,sup.img,sup.is_sale as sup_sale,sup.is_you,sup.weight,sup.is_virtual,sup.is_dingjin,sup.is_limit,';
        $field .= 'sup.app_rate,sup.price as market_price,if(vs_id != 0, sup.price, sup.price * ' . $GLOBALS['discount'] . ') AS u_price,sup.marketing_type ';
        $where = [
            ['sup.is_delete', '=', 0],
            ['shp.id', '=', $gshp_id],
        ];

        return $this->name('goods_shp')->alias('shp')->field($field)->leftJoin('ecs_goods_sup sup', 'shp.goods_id = sup.id')->where($where)->find();
    }

    public function collect_count($gshp_id)
    {
        return $this->name('collect_goods')->where('goods_id', $gshp_id)->count();
    }

    public function click($gshp_id)
    {
        $this->name('goods_shp')->where('id', $gshp_id)->setInc('hits');
    }

    //获取商品信息
    public function get_goods_content(array $gsup_ids)
    {

        $field = ' shp.id,shp.goods_id,sup.id as sup_id,shp.shops_id,sup.name,sup.shortname,sup.price_temp,sup.is_sale,sup.stock,sup.brand,sup.type,sup.icon,sup.is_dingjin,';
        $field .= ' if(vs_id != 0, sup.price, sup.price * ' . $GLOBALS['discount'] . ') AS u_price,sup.stock, sup.price as market_price,sup.app_rate,sup.vs_id, ';
        $field .= 'sup.img as thumbnail';

        $where = [
            ['sup.id', 'in', $gsup_ids],
            ['sup.is_sale', 'in', [0, 2]]
        ];

        $res = $this->name('goods_shp')->alias('shp')->field($field)
            ->leftJoin('ecs_goods_sup sup', 'shp.goods_id = sup.id')->where($where)->orderField('sup.id',$gsup_ids)->select()->toArray();
        if ($res) {
            foreach ($res as &$item) {
                $activities_list = $this->activities_list($item['sup_id']);
                $arr['activities_list'] = $activities_list['list'];
                $watermark = $activities_list['watermark'];
                $item['presale'] = 0;
                $item['is_dingjin'] = 0;


                $price = $this->get_goods_final_price($item['sup_id'], $item['u_price'], $item['market_price'], [], $item['app_rate'], 0, $item['vs_id']);

                $item['price'] = $this->price_format($price);
                $item['market_price'] = $this->price_format($item['market_price']);

                $item['mark'] = mark($item['is_sale'], $item['presale'], $item['is_dingjin'], $item['stock']);
                $item['after_mark'] = $activities_list['after_mark'];
                $item['thumbnail'] = empty($item['thumbnail']) ? '' : build_img_uri($item['thumbnail'], $item['stock'], $watermark, $item['is_sale']);
            }

            return $res;
        } else {
            return false;
        }
    }


    public function goods_attr_sort($attr_id)
    {

        $where = [
            ['a.attr_type', '=', 1],
            ['v.goods_attr_id', 'in', $attr_id],
        ];
        $goods_attr_id = $this->name('attribute')->alias('a')
            ->leftJoin('ecs_goods_attr v', 'v.attr_id = a.attr_id')->where($where)->order('a.attr_id ASC')->column('goods_attr_id');
        return implode('|', $goods_attr_id);
    }

    /**
     * 改变订单中商品库存
     * @param   int $order_id 订单号
     * @param   bool $is_dec 是否减少库存
     * @param   int $storage 减库存的时机，1，下订单时；0，发货时；
     */
    public function change_order_goods_storage($order_id, $is_dec = true, $storage = 0, $is_fa = false)
    {
        /* 查询订单商品信息 */
        switch ($storage) {
            case 0 :
                $fields = 'g.goods_id, SUM(d.send_number) AS num, g.product_id, g.presale_id ';
                $res = $this->name('order_goods')->alias('g')->field($fields)
                    ->leftJoin(' ecs_delivery_goods d', 'd.orec_id = g.rec_id')
                    ->where('g.order_id', $order_id)->group('g.goods_id, g.product_id')->select();
                break;

            case 1 :
                $fields = 'og.goods_id,SUM(goods_number) AS num, product_id, presale_id,shp.goods_id as gsup_id';
                $res = $this->name('order_goods')->alias('og')->field($fields)->leftJoin('ecs_goods_shp shp', 'og.goods_id = shp.id')
                    ->where('order_id', $order_id)->group('goods_id, product_id, presale_id')->select();

                break;
            case 2 :
                $fields = 'og.goods_id,SUM(goods_number) AS num, og.product_id, og.presale_id,shp.goods_id as gsup_id';
                $res = $this->name('order_goods')->alias('og')->field($fields)->leftJoin('ecs_goods_shp shp', 'og.goods_id = shp.id')
                    ->rightJoin('ecs_refund_goods rg', 'og.rec_id = rg.rec_id')
                    ->where('order_id', $order_id)->group('goods_id, product_id, presale_id')->select();
        }

        foreach ($res as $row) {

            //echo "sdsd";
            //	echo $row['goods_id'];

            if ($is_dec) {
                $this->change_goods_storage($row['gsup_id'], $row['product_id'], -$row['num'], $row['presale_id']);
            } else {

                return $this->change_goods_storage($row['gsup_id'], $row['product_id'], $row['num'], $row['presale_id']);
            }

        }


        //已发货的库存再减掉
        if ($is_fa) {

            $sql = "select dg.* from ecs_delivery_goods as dg " .
                " left join ecs_delivery_order as d on d.delivery_id=dg.delivery_id " .
                " where d.order_id=$order_id  and d.status=" . code::NDS_FINISH_D;

            $als = $this->query($sql);
            foreach ($als as $v2) {
                $this->change_goods_storage($v2['goods_id'], $v2['product_id'], -$v2['send_number'], $v2['presale_id']);
            }

        }

    }


    /**
     * 商品库存增与减 货品库存增与减
     *
     * @param   int $gsup_id 商品ID
     * @param   int $product_id 货品ID
     * @param   int $number 增减数量，默认0；
     * @param   int $presale_id
     * @return  bool
     */
    public function change_goods_storage($gsup_id, $product_id, $number = 0, $presale_id = 0)
    {

        if ($number == 0) {
            return true; // 值为0即不做、增减操作，返回true
        }

        if (empty($gsup_id) || empty($number)) {
            return false;
        }

        $number = ($number > 0) ? '+ ' . $number : $number;

        /* 处理货品库存 */
        $products_query = true;
        $where = [];
        if (empty($presale_id)) {

            if (!empty($product_id)) {
                array_push($where, ['product_id', '=', $product_id]);
                /*-减少库存-*/
                if ($number < 0) {
                    array_push($where, ['stock', '>=', abs($number)]);

                    $flag = $this->name('products')->where($where)->update(['stock' => Db::raw("stock $number")]);

                    if (!$flag) {
                        abort(500, '库存不足');
                    }

                } else {
                    /*-恢复库存-*/
                    $this->name('products')->where($where)->update(['stock' => Db::raw("stock $number")]);
                }
            } else {

                /* 处理商品库存 */
                array_push($where, ['id', '=', $gsup_id]);
                if ($number < 0) {
                    array_push($where, ['stock', '>=', abs($number)]);
                    $flag = $this->name('goods_sup')->where($where)->update(['stock' => Db::raw("stock $number")]);
                    if (!$flag) {
                        abort(500, '库存不足');
                    }
                } else {
                    $this->name('goods_sup')->where('id', $gsup_id)->update(['stock' => Db::raw("stock $number")]);
                }
            }


        } else {

            array_push($where, ['id', '=', $presale_id]);
            if ($number < 0) {

                array_push($where, ['stock', '>=', abs($number)]);

                $flag = $this->name('goods_presale')->where($where)->update(['stock' => Db::raw("stock $number")]);

                if (!$flag) {
                    abort(500, '库存不足');
                }
            } else {
                $this->name('goods_presale')->where($where)->update(['stock' => Db::raw("stock $number")]);
            }
        }

        /*-减少库存时 增加销售量-*/
        if ($number < 0) {
            $number = abs($number);
            $this->name('goods_shp')->where('goods_id', $gsup_id)->update(['base_sale' =>Db::raw("base_sale+$number")]);
        }
    }

    public function marketing_setting($gsup_id, int $marketing_type)
    {

        return $this->name('goods_sup')
            ->where(function ($query) use ($gsup_id) {
                if (is_array($gsup_id)) {
                    /* @var $query Query */
                    $query->where('id', 'in', $gsup_id);
                } else {
                    /* @var $query Query */
                    $query->where('id', $gsup_id);
                }
            })
            ->update(['marketing_type' => $marketing_type]);
    }


}