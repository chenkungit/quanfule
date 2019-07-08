<?php

namespace app\common\model\v2;

use app\common\model\Crowdfunding;
use app\common\model\Favourable;
use app\common\model\Flash;
use app\common\model\Freeship;
use app\common\model\Goods;
use app\common\model\Group;
use app\common\model\Load;
use app\common\Utils\PriceHelper;
use think\Db;
use app\common\model\Dj;
use app\common\model\Present;

class Cart extends Load
{
    const INVALID_PARAMETER = 2002;

    /*--*/
    public function once_add($gshp_id, $number, $attr_id, $presale, $is_dingjin, $is_group, $group_id = 0, $delivery_id = '')
    {
        $Goods = new Goods();
        $main_goods_info = $Goods->main_goods_info($gshp_id);
        /*- 团购限时购商品默认为1件 */
        $marketing_price = 0;
        $number = in_array($is_group, [1, 3]) ? 1 : $number;

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

        if ($main_goods_info['is_limit'] == 1) {
            $limit_number = $Goods->check_limit_buy($main_goods_info['gsup_id']);
            if ($limit_number) {
                if ($number > $limit_number) {
                    return json_error('', '超过限购数量');
                }
            }
        }


        $presale = 0;
        $product_arr = $this->name('products')->where('goods_id', $gsup_id)->select();
        if (count($product_arr) > 0) {

            if (empty($attr_id)) {
                return json_error('', '请选择商品属性', self::INVALID_PARAMETER);
            } else {
                $goods_attr = $Goods->goods_attr_sort($attr_id);
                $product_info = $this->name('products')->where([['goods_id', '=', $gsup_id], ['goods_attr', '=', $goods_attr]])->find();

                if (empty($product_info)) {
                    return json_error('', '选择属性不全', self::INVALID_PARAMETER);
                } else if ($product_info['stock'] == 0) {
                    return json_error('', '该商品已售完', self::INVALID_PARAMETER);
                }
                $main_goods_info['stock'] = $product_info['stock'];
            }
        }


        if (!isset($product_info)) {
            $attr_id = [];
            $product_info = array('product_id' => 0, 'goods_attr' => '', 'product_sn' => '', 'stock' => 0);
        }

        if ($main_goods_info['stock'] == 0) {
            return json_error('', '该商品已售完', self::INVALID_PARAMETER);
        }

        $goods_price = $this->get_goods_final_price($gsup_id, $main_goods_info['u_price'], $main_goods_info['market_price'], $attr_id, 0, $marketing_price, 0);//使用实际价格

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
        ];

        if ($number > $main_goods_info['stock']) {
            return json_error('', '购买数量超过库存', self::INVALID_PARAMETER);
        }

        /*-删除临时表中的商品 再添加-*/
        $this->name('once_cart')->where('user_id', $GLOBALS['user_id'])->delete();

        if ($main_goods_info['is_you'] == 1) {
            $you_ratio = $this->get_duomaiyouhui($gsup_id, $number);
            $goods_price = $goods_price * $you_ratio;
        }

        $parent['goods_price'] = $goods_price;
        $parent['goods_number'] = $number;
        $this->name('once_cart')->insert($parent);

        return true;
    }


    public function get_cart_goods_checked($sendaddress_id, $is_dingjin = 0, $once = 0, $is_group = 0)
    {
        $this->recalculate_price('', $once);

        if ($once) {
            $db = $this->name('once_cart');
        } else {
            $db = $this->name('cart');
        }


        /* 用于统计购物车中实体商品和虚拟商品的个数 */
        $virtual_goods_count = 0;
        $real_goods_count = 0;

        $join = [
            ['ecs_suppliers supr', 'c.suppliers_id = supr.id', 'left'],
            ['ecs_sendaddress sd', 'sd.id = supr.sendaddress', 'left'],
        ];

        $sendaddress_arr = $db->field('supr.sendaddress,sd.address')->alias('c')->join($join)->where([['c.user_id', '=', $GLOBALS['user_id']], ['supr.sendaddress', '=', $sendaddress_id]])->find()->toArray();

        if (!$sendaddress_arr) {
            abort(404, '商品不存在');
        }

        $sendaddress_arr['vip_goods_info'] = [];
        $sendaddress_arr['goods_total'] = 0; //商品总价
        $sendaddress_arr['vip_goods_total'] = 0; //vip商品总价
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

        $field = 'c.rec_id,sup.id as sup_id,c.goods_id,c.shops_id,c.suppliers_id,c.goods_name,c.goods_sn,c.product_id,sup.brand,sup.category,';
        $field .= 'c.goods_number,c.goods_price,sup.price as market_price,c.weight,c.is_virtual,c.goods_attr,c.goods_attr_id,c.is_freeshipping,c.presale_id,c.type,c.shouwan,';
        $field .= 'c.is_group,c.group_id,c.is_dingjin,';
        $field .= "sup.use_points,sup.vs_id,sup.rank_points,sup.thumbnail as goods_thumb";
        $where = [
            ['supr.sendaddress', '=', $sendaddress_arr['sendaddress']],
            ['c.user_id', '=', $GLOBALS['user_id']],
            ['c.checked', '=', 1]
        ];

        $join = [
            ['ecs_suppliers supr', 'c.suppliers_id = supr.id', 'left'],
            ['ecs_shops sp', 'c.shops_id = sp.id', 'left'],
            ['ecs_goods_shp shp', 'c.goods_id = shp.id', 'left'],
            ['ecs_goods_sup sup', 'sup.id=shp.goods_id', 'left'],
        ];

        $db->removeOption(true);
        $res = $db->alias('c')->field($field)->where($where)->join($join)->order('rec_id desc')->select();

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
                $arr[$key]['discount'] = 0;
                $row['market_price'] = $row['product_id'] ? $this->name('products')->where('product_id', $row['product_id'])->value('price') : $row['market_price'];
                $arr[$key]['market_price'] = (string)number_format($row['market_price'], 2, ".", "");
                $arr[$key]['market_goods_total'] = $row['market_price'] * $row['goods_number'];
                $arr[$key]['goods_total'] = $row['goods_price'] * $row['goods_number'];
                $arr[$key]['subtotal_format'] = $this->price_format($row['goods_price'] * $row['goods_number'], false);
                $arr[$key]['subweight'] = $row['weight'] * $row['goods_number'];
                $arr[$key]['sub_use_points'] = $row['use_points'] * $row['goods_number'];
                $arr[$key]['sub_rank_points'] = $row['rank_points'] * $row['goods_number'];
                $arr[$key]['vs_id'] = $row['vs_id'];


                if ($row['vs_id']) {
                    $sendaddress_arr['vip_goods_info'][$row['sup_id']] = $row['vs_id'];
                    $sendaddress_arr['vip_goods_total'] += $arr[$key]['goods_total'];
                }

                $arr[$key]['subtotal'] = $arr[$key]['goods_total'];

                $arr[$key]['goods_price_format'] = PriceHelper::format($row['goods_price']);

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
            }

            $sendaddress_arr['goods_detail'] = array_values($arr);
//                $sendaddress_arr['subweight'] = round($sendaddress_arr['subweight'],2);
            $sendaddress_arr['difference'] = round($sendaddress_arr['market_subtotal'] - $sendaddress_arr['subtotal'], 2);
            $sendaddress_arr['difference_format'] = PriceHelper::format($sendaddress_arr['difference']);
            $sendaddress_arr['freeshipping_subweight'] = round($sendaddress_arr['freeshipping_subweight'], 2);
            $sendaddress_arr['market_subtotal_format'] = PriceHelper::format($sendaddress_arr['market_subtotal']);
            $sendaddress_arr['subtotal'] = $sendaddress_arr['subtotal'] - $sendaddress_arr['discount'];
            $sendaddress_arr['subtotal_format'] = PriceHelper::format($sendaddress_arr['subtotal']);

        }

        return $sendaddress_arr;
    }

}