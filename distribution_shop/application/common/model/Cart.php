<?php

namespace app\common\model;

use app\common\Utils\PriceHelper;

class Cart extends Load
{
    const INVALID_PARAMETER = 2002;

    /*--*/
    public function once_add($gshp_id, $number, $attr_id, $presale, $is_dingjin, $libao)
    {

        $main_goods_info = (new Goods())->main_goods_info($gshp_id);

        if (empty($main_goods_info)) {
            abort(404, '该商品不存在');
        }
        $gsup_id = $main_goods_info['gsup_id'];

        if (!empty($main_goods_info['start_num']) && $number < $main_goods_info['start_num']) {
            return json_error('', '起订量为' . $main_goods_info['start_num'] . '以上');
        }
        if (!empty($main_goods_info['Nbei'])) {
            if ($number % $main_goods_info['Nbei'] != 0) {
                return json_error('', '起订量为' . $main_goods_info['Nbei'] . '的倍数');
            }
        }

        if ($main_goods_info['sup_sale'] == 1) {
            return json_error('', '该商品已下架');
        }
        if ($main_goods_info['sup_sale'] == 2) {
            return json_error('', '该商品还未上架');
        }

        $stock = $this->name('products')->where('goods_id', $gsup_id)->sum('stock');
        if (!empty($stock)) {
            if (empty($attr_id) || !is_array($attr_id)) {
                return json_error('', '请选择商品属性', self::INVALID_PARAMETER);
            } else {
                $goods_attr = implode('|', $attr_id);

                $a_goods_attr = implode('|', array_reverse($attr_id));

                $product_info = $this->name('products')->where([['goods_id', '=', $gsup_id], ['goods_attr', 'in', [$goods_attr, $a_goods_attr]]])->find();

                if (empty($product_info)) {
                    return json_error('', '选择属性不全', self::INVALID_PARAMETER);
                } else if ($product_info['stock'] == 0) {
                    return json_error('', '该商品已售完', self::INVALID_PARAMETER);
                }
            }
        }


        /*查找是否存在预售产品*/
        $presale_info = $this->name('goods_presale')->where('goods_id', $gsup_id)->column('id');

        if (!empty($presale_info)) {
            if (empty($presale)) {
                return json_error('', '请选择预售属性', self::INVALID_PARAMETER);
            } else {
                if (count($presale) == 1) {
                    $presale = $presale[0];
                    $pre_info = $this->name('goods_presale')->field('stock,product_id')->where('id', $presale)->find();
                    if ($pre_info['product_id']) {
                        $product_info = $this->name('products')->where('id', $pre_info['product_id'])->find();
                    }
                    $main_goods_info['stock'] = $pre_info['stock'];
                } else {
                    if (empty($attr_id)) {
                        return json_error('', '请选择商品属性', self::INVALID_PARAMETER);
                    }
                    foreach ($presale_info as $presale_id) {

                        $tmp_info = $this->name('goods_presale')->alias('gp')
                            ->field('gp.id, p.goods_attr, gp.stock')
                            ->join('ecs_products p', 'p.product_id = gp.product_id', 'left')->where('gp.id', $presale_id)->find();
                        $goods_attr_arr = explode("|", $tmp_info['goods_attr']);
                        if (check_arr_equal($goods_attr_arr, $attr_id)) {
                            //获取预售id
                            $presale = $tmp_info['id'];
                            //检查预售库存
                            $main_goods_info['stock'] = intval($tmp_info['stock']);
                            break;
                        }

                    }
                }
            }
        } else {
            $presale = 0;
        }

        if (empty($stock)) {
//            $this->data['attr_id'] = [];
            $product_info = array('product_id' => 0, 'goods_attr' => '', 'product_sn' => '', 'stock' => 0);
        }

        if ($main_goods_info['stock'] == 0) {
            return json_error('', '该商品已售完', self::INVALID_PARAMETER);
        }

        $goods_price = $this->get_goods_final_price($gsup_id, $main_goods_info['u_price'], $main_goods_info['market_price'], $attr_id, 0, 0, $main_goods_info['is_fei5zhe']);//使用实际价格

        $weight = $this->get_weight_goods($gsup_id, $product_info['product_id']);

        $goods_attr = $this->get_goods_attr_info($attr_id);
        $goods_attr_id = join(',', $attr_id);

        /* 初始化要插入购物车的基本件数据 */
        $parent = [
            'user_id' => $GLOBALS['user_id'],
            'session_id' => $GLOBALS['key'],
            'goods_id' => $gshp_id,
            'shops_id' => $main_goods_info['shops_id'],
            'suppliers_id' => $main_goods_info['suppliers_id'],
            'goods_sn' => empty($product_info['product_sn']) ? addslashes($main_goods_info['code']) : addslashes($product_info['product_sn']),
            'product_id' => $product_info['product_id'],
            'goods_name' => addslashes($main_goods_info['name']),
            'goods_attr' => addslashes($goods_attr),
            'goods_attr_id' => $goods_attr_id,
            'is_virtual' => $main_goods_info['is_virtual'],
            'is_freeshipping' => $main_goods_info['is_freeshipping'],
            'presale_id' => $presale,
            'libao' => 0,
            'checked' => 1,
            'weight' => $weight,
            'add_time' => time(),
            'is_dingjin' => $is_dingjin,
        ];

        /* 检查该商品是否已经存在在购物车中 */
        $where = [
            ['goods_id', '=', $gshp_id],
            ['presale_id', '=', $presale],
            ['goods_attr_id', '=', $goods_attr_id],
            ['libao', '=', 0],
            ['is_dingjin', '=', $is_dingjin],
            ['user_id', '=', $GLOBALS['user_id']],
        ];
        $row = $this->name('cart')->field('goods_number,rec_id')->where($where)->find();

        if ($number > $main_goods_info['stock']) {
            return json_error('', '购买数量超过库存', self::INVALID_PARAMETER);
        }
        if ($row) {
            $this->name('cart')->where('rec_id', $row['rec_id'])->delete();
        }

        if ($main_goods_info['is_you'] == 1) {
            $you_ratio = $this->get_duomaiyouhui($gsup_id, $number);
            $goods_price = $goods_price * $you_ratio;
        }

        $parent['goods_price'] = $goods_price;
        $parent['goods_number'] = $number;
        $iid = $this->name('cart')->insertGetId($parent);

        if ($libao)//礼包商品必须更新礼包id
        {
            $update['libao'] = $iid;
            array_push($where, ['rec_id', '=', $iid]);
            db('cart')->where($where)->update($update);
        }

        //处理买一送一
        //addto_buyone_giveone($gsup_id,$iid);
        if ($main_goods_info['is_virtual'] > 0)//虚拟商品清楚checked全部为0
        {
            $update = [
                'checked' => 0
            ];
            array_push($where, ['rec_id', 'neq', $iid], ['user_id', '=', $GLOBALS['user_id']]);

            db('cart')->where($where)->update($update);
        }

        $update = [
            'checked' => 0
        ];
        array_push($where, ['rec_id', 'neq', $iid], ['user_id', '=', $GLOBALS['user_id']]);

        db('cart')->where($where)->update($update);


        return true;
    }


    public function get_cart_goods_all()
    {
        $favourable = new Favourable();
        $freeShip = new Freeship();
        $present = new Present();
        /* 初始化 */
        $goods_list = array();
        $total = array(
            'total_price' => 0, // 本店售价合计（有格式）
            'total_number' => 0,
            'checked_total_price' => 0,
            'checked_total_number' => 0,
        );

        /* 用于统计购物车中实体商品和虚拟商品的个数 */
        $virtual_goods_count = 0;
        $real_goods_count = 0;

        $where['c.user_id'] = $GLOBALS['user_id'];

        $sendaddress_arr = $this->name('cart')->field('supr.sendaddress,sd.address,c.is_dingjin')->alias('c')
            ->leftJoin('ecs_suppliers supr', 'c.suppliers_id = supr.id')
            ->leftJoin('ecs_sendaddress sd', 'sd.id = supr.sendaddress')
            ->where('c.user_id', $GLOBALS['user_id'])->group('supr.sendaddress,c.is_dingjin')->order('supr.sendaddress asc')->select();

        if (empty($sendaddress_arr)) {
            return array('goods_list' => [], 'total' => $total);
        }

        foreach ($sendaddress_arr as $key0 => $sendaddress) {

            if ($sendaddress_arr[$key0]['is_dingjin'] == 1) {
                $sendaddress_arr[$key0]['address'] = $sendaddress_arr[$key0]['address'] . '(定金)';
            }

            $sendaddress_arr[$key0]['subtotal'] = 0;
            $sendaddress_arr[$key0]['all_fav'] = [];
            $sendaddress_arr[$key0]['all_pre'] = [];
            $sendaddress_arr[$key0]['present'] = [];
            $sendaddress_arr[$key0]['discount'] = 0;
            $sendaddress_arr[$key0]['subweight'] = 0;
            $sendaddress_arr[$key0]['subnumber'] = 0;
            $sendaddress_arr[$key0]['checked_subtotal'] = 0;
            $sendaddress_arr[$key0]['checked_subweight'] = 0;
            $sendaddress_arr[$key0]['checked_subnumber'] = 0;


            $field = 'c.rec_id,sup.id as sup_id,c.goods_id,c.shops_id,c.suppliers_id,c.goods_name,c.goods_sn,c.product_id,c.checked,c.is_dingjin,c.product_id,';
            $field .= 'c.goods_number,c.goods_price,sup.price as market_price,c.weight,c.is_virtual,c.goods_attr,c.goods_attr_id,c.presale_id,c.type,c.shouwan,';
            $field .= "sup.brand,sup.category,sup.start_num,sup.Nbei,sup.use_points,sup.rank_points," . concat_img('sup.thumbnail', 'goods_thumb');

            $where = [
                ['c.is_dingjin', '=', $sendaddress['is_dingjin']],
                ['supr.sendaddress', '=', $sendaddress['sendaddress']],
                ['c.user_id', '=', $GLOBALS['user_id']]
            ];

            $res = $this->name('cart')->alias('c')->field($field)->where($where)
                ->leftJoin('ecs_suppliers supr', 'c.suppliers_id = supr.id')
                ->leftJoin('ecs_shops sp', 'c.shops_id = sp.id')
                ->leftJoin('ecs_goods_shp shp', 'c.goods_id = shp.id')
                ->leftJoin('ecs_goods_sup sup', 'sup.id=shp.goods_id')
                ->order('rec_id desc')->select();
            $arr = [];
            if (!empty($res)) {
                foreach ($res as $key => &$row) {
                    $row['discount'] = 0;
                    $arr[$key] = $row;

                    $market_price = $row['product_id'] ? $this->name('products')->where('product_id', $row['product_id'])->value('price') : $row['market_price'];
                    $arr[$key]['market_price'] = PriceHelper::format($market_price);

                    $row['weight'] = round($row['weight'], 2);

                    $arr[$key]['subtotal'] = $row['goods_price'] * $row['goods_number'];

                    $arr[$key]['subtotal_format'] = PriceHelper::format($row['goods_price'] * $row['goods_number'], false);
                    $arr[$key]['subweight'] = $row['weight'] * $row['goods_number'];
                    $arr[$key]['goods_price_format'] = PriceHelper::format($row['goods_price']);
                    //$arr[$key]['goods_price_1']  = $row['goods_price'];

                    /*每个订单的重量和价格*/
                    $sendaddress_arr[$key0]['subtotal'] += $arr[$key]['subtotal'];
                    $sendaddress_arr[$key0]['subweight'] += $arr[$key]['subweight'];
                    $sendaddress_arr[$key0]['subnumber'] += $arr[$key]['goods_number'];
                    $total['total_price'] += $row['goods_price'] * $row['goods_number'];
                    $total['total_number'] += $row['goods_number'];
                    /* 统计实体商品和虚拟商品的个数 */

                    if ($row['checked'] == 1) {
                        $sendaddress_arr[$key0]['checked_subtotal'] += $arr[$key]['subtotal'];
                        $sendaddress_arr[$key0]['checked_subweight'] += $arr[$key]['subweight'];
                        $sendaddress_arr[$key0]['checked_subnumber'] += $arr[$key]['goods_number'];
                        $total['checked_total_price'] += $row['goods_price'] * $row['goods_number'];
                        $total['checked_total_number'] += $row['goods_number'];
                        if (!$sendaddress['is_dingjin']) {

                            /*-将活动变成商品的参数最后在操作-*/
                            $tmp = $freeShip->freeshipping_goods($row['sup_id']);

                            if ($tmp) {

                                $arr[$key]['freeshipping_activity'] = $tmp;
                                $arr[$key]['freeshipping_activity_name'] = $tmp[0]['activity_name'];
                                $arr[$key]['freeshipping_activity_id'] = $tmp[0]['fa_id'];
                                $arr[$key]['freeshipping_activity_type'] = $tmp[0]['type'];
                                $arr[$key]['freeshipping_activity_number'] = $tmp[0]['number'];
                                $arr[$key]['freeshipping_activity_amount'] = $tmp[0]['amount'];
                            }

                            $tmp2 = $favourable->fav_goods($row['sup_id']);

                            if ($tmp2) {

                                $arr[$key]['fav_activity_name'] = $tmp2['activity_name'];
                                $arr[$key]['fav_activity_id'] = $tmp2['fv_id'];
                                $arr[$key]['fav_type'] = $tmp2['type'];
                                $arr[$key]['fav_number'] = $tmp2['number'];
                                $arr[$key]['fav_amount'] = $tmp2['amount'];
                                $arr[$key]['fav_reduce_type'] = $tmp2['reduce_type'];
                                $arr[$key]['fav_is_repeat'] = $tmp2['is_repeat'];
                                $arr[$key]['fav_reduce_arg'] = $tmp2['reduce_arg'];
                            }

                            $tmp3 = $present->pre_goods($row['sup_id']);
                            if ($tmp3) {
                                $arr[$key]['pre_activity_name'] = $tmp3['activity_name'];
                                $arr[$key]['pre_activity_id'] = $tmp3['pa_id'];
                                $arr[$key]['pre_type'] = $tmp3['type'];
                                $arr[$key]['pre_number'] = $tmp3['number'];
                                $arr[$key]['pre_amount'] = $tmp3['amount'];
                                $arr[$key]['pre_present_sup_id'] = $tmp3['present_sup_id'];
                                $arr[$key]['pre_is_repeat'] = $tmp3['is_repeat'];
                            }
                        }
                    }

                    if ($row['is_virtual'] == 0) {
                        $real_goods_count++;
                    } else {
                        $virtual_goods_count++;
                    }

                    if (!empty($row['presale_id'])) {

                        $predate = $this->name('goods_presale')->where('id', $row['presale_id'])->value('predate');
                        if (!empty($predate)) {
                            $arr[$key]['predate'] = $predate;
                        }
                    }

                }

                if ($sendaddress['is_dingjin'] == 0) {
                    $all_fav = $favourable->all_fav($sendaddress_arr[$key0]['checked_subtotal'], $sendaddress_arr[$key0]['checked_subnumber']);

                    if ($all_fav) {
                        $sendaddress_arr[$key0]['all_fav'] = $all_fav;
                        $sendaddress_arr[$key0]['discount'] += array_sum(array_map(function ($val) {
                            return $val['discount_price'];
                        }, $sendaddress_arr[$key0]['all_fav']));
                        $all_dicount = $sendaddress_arr[$key0]['discount'];
                        if ($all_dicount) {
                            $checked_subtotal = $sendaddress_arr[$key0]['checked_subtotal'];
                            array_map(function (&$val) use ($all_dicount, $checked_subtotal) {
                                $val['discount'] += round($val['subtotal'] / $checked_subtotal * $all_dicount, 2);
                            }, $arr);
                        }
                    }

                    $all_pre = $present->all_pre($sendaddress_arr[$key0]['checked_subtotal'], $sendaddress_arr[$key0]['checked_subnumber']);
                    if ($all_pre) {
                        $sendaddress_arr[$key0]['all_pre'] = $all_pre['item'];
                        $sendaddress_arr[$key0]['present'] = array_merge($sendaddress_arr[$key0]['present'], $all_pre['present']);
                    }
                    $pre = $present->pre_arrange($arr);
                    $sendaddress_arr[$key0]['present'] = array_merge($sendaddress_arr[$key0]['present'], $pre['present']);

                    $fv = $favourable->fav_arrange($arr);
                    $sendaddress_arr[$key0]['discount'] += $fv['discount_price'];

//                return $arr;
                    $fa = $freeShip->freeshipping_arrange($arr);
                    $sendaddress_arr[$key0]['freeshipping_subweight'] = $fa['freeshipping_subweight'];
                    $arr = $fa['goods_list'];
                    //$sendaddress_arr[] = $res;
                } else {
                    $sendaddress_arr[$key0]['subtotal_dj'] = round(config('others.DJ_RATE') * $sendaddress_arr[$key0]['subtotal'], 2);

                    $sendaddress_arr[$key0]['subtotal_wk'] = round($sendaddress_arr[$key0]['subtotal'] - $sendaddress_arr[$key0]['subtotal_dj'], 2);
                    $sendaddress_arr[$key0]['dj_name'] = $dj->is_dj($arr[0]['sup_id'])['dj_name'];
                }
                $sendaddress_arr[$key0]['checked_subtotal'] = round($sendaddress_arr[$key0]['checked_subtotal'], 2);
                $sendaddress_arr[$key0]['subtotal'] = round($sendaddress_arr[$key0]['subtotal'], 2);

                $arr = array_values($arr);
                $sendaddress_arr[$key0]['goods_detail'] = $arr;

            }

        }

        $total['total_price'] = round($total['total_price'], 2);
        $total['total_price_fromat'] = PriceHelper::format($total['total_price']);
        $total['checked_total_price'] = round($total['checked_total_price'], 2);
        $total['checked_total_price_fromat'] = PriceHelper::format($total['checked_total_price']);

        // $total['market_price'] = $this->price_format($total['market_price'], false);
        $total['real_goods_count'] = $real_goods_count;
        $total['virtual_goods_count'] = $virtual_goods_count;

        return array('goods_list' => $sendaddress_arr, 'total' => $total);


    }

    public function get_cart_goods_checked($sendaddress_id, $is_dingjin = 0)
    {
        /* 初始化  */
        if (isset($GLOBALS['heiwuzhe'])) {
            $this->recalculate_price();
        }


        $favourable = new Favourable();
        $freeShip = new Freeship();
        $present = new Present();
//        $goods_list = array();

        /* 用于统计购物车中实体商品和虚拟商品的个数 */
        $virtual_goods_count = 0;
        $real_goods_count = 0;

        $sendaddress_arr = $this->name('cart')->field('supr.sendaddress,sd.address')->alias('c')
            ->leftJoin('ecs_suppliers supr', 'c.suppliers_id = supr.id')
            ->leftJoin('ecs_sendaddress sd', 'sd.id = supr.sendaddress')
            ->where([['c.user_id', '=', $GLOBALS['user_id']], ['supr.sendaddress', '=', $sendaddress_id]])->find();

        if (!$sendaddress_arr) {
            abort(404, '商品不存在');
        }

        $sendaddress_arr['goods_total'] = 0; //商品总价
        $sendaddress_arr['all_fav'] = [];
        $sendaddress_arr['all_pre'] = [];
        $sendaddress_arr['present'] = [];
        $sendaddress_arr['sub_shipping_fee'] = 0; //运费总价
        $sendaddress_arr['subtotal'] = 0;   //总价
        $sendaddress_arr['market_subtotal'] = 0;   //市场总价
        $sendaddress_arr['subweight'] = 0;  //总重量
        $sendaddress_arr['freeshipping_subweight'] = 0;
        $sendaddress_arr['subnumber'] = 0;  //总数量
        $sendaddress_arr['discount'] = 0;   //折扣
        $sendaddress_arr['difference'] = 0;
        $sendaddress_arr['sub_use_points'] = 0;//积分1
        $sendaddress_arr['sub_rank_points'] = 0;//积分2
        $sendaddress_arr['subtotal_dj'] = 0;
        $sendaddress_arr['subtotal_wk'] = 0;

        $field = 'c.rec_id,sup.id as sup_id,c.goods_id,c.shops_id,c.suppliers_id,c.goods_name,c.goods_sn,c.product_id,sup.brand,sup.category,c.product_id,';
        $field .= 'c.goods_number,c.goods_price,sup.price as market_price,c.weight,c.is_virtual,c.goods_attr,c.goods_attr_id,c.is_freeshipping,c.presale_id,c.type,c.shouwan,';
        $field .= 'c.is_dingjin,';
        $field .= "sup.use_points,sup.rank_points," . concat_img('sup.thumbnail', 'goods_thumb');

        $where = [['c.is_dingjin', '=', $is_dingjin], ['supr.sendaddress', '=', $sendaddress_arr['sendaddress']], ['c.checked', '=', 1], ['c.user_id', '=', $GLOBALS['user_id']]];


        $res = $this->name('cart')->alias('c')->field($field)->where($where)
            ->leftJoin('ecs_suppliers supr', 'c.suppliers_id = supr.id')
            ->leftJoin('ecs_shops sp', 'c.shops_id = sp.id')
            ->leftJoin('ecs_goods_shp shp', 'c.goods_id= shp.id')
            ->leftJoin('ecs_goods_sup sup', 'shp.goods_id= sup.id')
            ->order('rec_id desc')->select();

        if (!$res) {
            abort(404, '商品不存在');
        }
        $arr = [];
        if (!empty($res)) {
            foreach ($res as $key => $row) {
                if ($row['shouwan'] == 1) {
                    abort(400, '该商品已售完');
                }
                $row['discount'] = 0;
                $arr[$key] = $row;


                $row['weight'] = round($row['weight'], 3);
                $row['market_price'] = $row['product_id'] ? $this->name('products')->where('product_id', $row['product_id'])->value('price') : $row['market_price'];
                $arr[$key]['market_price'] = (string)number_format($row['market_price'], 2);
                $arr[$key]['market_goods_total'] = $row['market_price'] * $row['goods_number'];
                $arr[$key]['goods_total'] = $row['goods_price'] * $row['goods_number'];
                $arr[$key]['subtotal_format'] = $this->price_format($row['goods_price'] * $row['goods_number'], false);
                $arr[$key]['subweight'] = $row['weight'] * $row['goods_number'];
                $arr[$key]['sub_use_points'] = $row['use_points'] * $row['goods_number'];
                $arr[$key]['sub_rank_points'] = $row['rank_points'] * $row['goods_number'];

                /*-定金商品不参加任何活动 不能使用优惠券-*/
                if (!$is_dingjin) {
                    /*-将活动变成商品的参数最后在操作-*/
                    $tmp = $freeShip->freeshipping_goods($row['sup_id']);

                    if ($tmp) {
                        $arr[$key]['freeshipping_activity_name'] = $tmp['freeshipping_activity_name'];
                        $arr[$key]['freeshipping_activity_id'] = $tmp['freeshipping_activity_id'];
                        $arr[$key]['freeshipping_activity_type'] = $tmp['freeshipping_type'];
                        $arr[$key]['freeshipping_activity_number'] = $tmp['freeshipping_number'];
                        $arr[$key]['freeshipping_activity_amount'] = $tmp['freeshipping_amount'];
                    }

                    $tmp2 = $favourable->fav_goods($row['sup_id']);

                    if ($tmp2) {
                        $arr[$key]['fav_activity_name'] = $tmp2['activity_name'];
                        $arr[$key]['fav_activity_id'] = $tmp2['fv_id'];
                        $arr[$key]['fav_type'] = $tmp2['type'];
                        $arr[$key]['fav_number'] = $tmp2['number'];
                        $arr[$key]['fav_amount'] = $tmp2['amount'];
                        $arr[$key]['fav_reduce_type'] = $tmp2['reduce_type'];
                        $arr[$key]['fav_is_repeat'] = $tmp2['is_repeat'];
                        $arr[$key]['fav_reduce_arg'] = $tmp2['reduce_arg'];
                    }

                    $tmp3 = $present->pre_goods($row['sup_id']);
                    if ($tmp3) {
                        $arr[$key]['pre_activity_name'] = $tmp3['activity_name'];
                        $arr[$key]['pre_activity_id'] = $tmp3['pa_id'];
                        $arr[$key]['pre_type'] = $tmp3['type'];
                        $arr[$key]['pre_number'] = $tmp3['number'];
                        $arr[$key]['pre_amount'] = $tmp3['amount'];
                        $arr[$key]['pre_present_sup_id'] = $tmp3['present_sup_id'];
                        $arr[$key]['pre_is_repeat'] = $tmp3['is_repeat'];
                    }
                } else {
                    $arr[$key]['dj'] = round(config('others.DJ_RATE') * $arr[$key]['goods_total'], 2);
                    $arr[$key]['wk'] = round($arr[$key]['goods_total'] - $arr[$key]['dj'], 2);
                }

                $arr[$key]['subtotal'] = $arr[$key]['goods_total'];

                $arr[$key]['goods_price_format'] = $this->price_format($row['goods_price']);
                //$res[$key]['goods_price_1']  = $row['goods_price'];

                /*订单的总重量和价格和积分*/
                $sendaddress_arr['market_subtotal'] += $arr[$key]['market_goods_total'];
                $sendaddress_arr['goods_total'] += $arr[$key]['goods_total'];
//                    $sendaddress_arr['sub_shipping_fee'] += $arr[$key]['shipping_fee'];
                $sendaddress_arr['subtotal'] += $arr[$key]['subtotal'];
                $sendaddress_arr['subweight'] += $arr[$key]['subweight'];

                $sendaddress_arr['subnumber'] += $row['goods_number'];
                $sendaddress_arr['sub_use_points'] += $arr[$key]['sub_use_points'];
                $sendaddress_arr['sub_rank_points'] += $arr[$key]['sub_rank_points'];

                /* 统计实体商品和虚拟商品的个数 */

                if ($row['is_virtual'] == 0) {
                    $real_goods_count++;
                } else {
                    $virtual_goods_count++;
                }
                //查询发货期
                if (!empty($row['presale_id'])) {
                    $predate = $this->name('goods_presale')->where('id', $row['presale_id'])->value('predate');
                    if (!empty($predate)) {
                        $res[$key]['predate'] = $predate;
                    }
                }

            }

            if (!$is_dingjin) {

                $all_fav = $favourable->all_fav($sendaddress_arr['goods_total'], $sendaddress_arr['subnumber']);

                if ($all_fav) {
                    $sendaddress_arr['all_fav'] = $all_fav;
                    $sendaddress_arr['discount'] += array_sum(array_map(function ($val) {
                        return $val['discount_price'];
                    }, $sendaddress_arr['all_fav']));
                    $all_dicount = $sendaddress_arr['discount'];
                    $checked_subtotal = $sendaddress_arr['checked_subtotal'];
                    array_map(function (&$val) use ($all_dicount, $checked_subtotal) {
                        $val['discount'] += round($val['subtotal'] / $checked_subtotal * $all_dicount, 2);
                    }, $arr);
                }

                $all_pre = $present->all_pre($sendaddress_arr['goods_total'], $sendaddress_arr['subnumber']);
                if ($all_pre) {
                    $sendaddress_arr['all_pre'] = $all_pre['item'];
                    $sendaddress_arr['present'] = array_merge($sendaddress_arr['present'], $all_pre['present']);
                }

                $pre = $present->pre_arrange($arr);
                $sendaddress_arr['present'] = array_merge($sendaddress_arr['present'], $pre['present']);

                $fv = $favourable->fav_arrange($arr);
                $sendaddress_arr['discount'] += $fv['discount_price'];

                //                return $arr;
                $fa = $freeShip->freeshipping_arrange($arr);
                $sendaddress_arr['freeshipping_subweight'] = $fa['freeshipping_subweight'];
                $arr = $fa['goods_list'];
            } else {
                $sendaddress_arr['subtotal_dj'] = round(config('others.DJ_RATE') * $sendaddress_arr['subtotal'], 2);
                $sendaddress_arr['subtotal_wk'] = round($sendaddress_arr['subtotal'] - $sendaddress_arr['subtotal_dj'], 2);
                $sendaddress_arr['dj_name'] = $dj->is_dj($arr[0]['sup_id'])['dj_name'];
            }

            $sendaddress_arr['goods_detail'] = array_values($arr);
//                $sendaddress_arr['subweight'] = round($sendaddress_arr['subweight'],2);
            $sendaddress_arr['difference'] = round($sendaddress_arr['market_subtotal'] - $sendaddress_arr['subtotal'], 2);
            $sendaddress_arr['freeshipping_subweight'] = round($sendaddress_arr['freeshipping_subweight'], 2);
            $sendaddress_arr['market_subtotal_format'] = $this->price_format($sendaddress_arr['market_subtotal']);
            $sendaddress_arr['subtotal_format'] = $this->price_format($sendaddress_arr['subtotal']);

        }

        return $sendaddress_arr;
    }


    public function set_cart_goods_checked($checked_ids_arr = [])
    {

        $this->name('cart')->where('user_id', $GLOBALS['user_id'])->update(['checked' => 0]);
        if (!empty($checked_ids_arr)) {
            $this->name('cart')->where([['rec_id', 'in', implode(',', $checked_ids_arr)], ['user_id', '=', $GLOBALS['user_id']]])->update(['checked' => 1]);
        }
    }
}