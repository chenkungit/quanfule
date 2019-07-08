<?php

namespace app\common\model;

use GuzzleHttp\Client;
use think\Model;

class Load extends Model
{
    public $_CFG;

    public function __construct($data = [])
    {
        parent::__construct($data);
//        $this->_CFG = load_config();
    }

    /**
     * 格式化商品价格
     *
     * @access  public
     * @param   float $price 商品价格
     * @return  string
     */
    public function price_format($price, $change_price = true)
    {
        if ($price === '') {
            $price = 0;
        }
//        && defined('ECS_ADMIN') === false
        if ($change_price) {
//            $_CFG['price_format']=4;//?
            switch (3) {
                case 0:
                    $price = number_format($price, 2, '.', '');
                    break;
                case 1: // 保留不为 0 的尾数
                    $price = preg_replace('/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format($price, 2, '.', ''));

                    if (substr($price, -1) == '.') {
                        $price = substr($price, 0, -1);
                    }
                    break;
                case 2: // 不四舍五入，保留1位
                    $price = substr(number_format($price, 2, '.', ''), 0, -1);
                    break;
                case 3: // 直接取整
                    $price = floatval($price);
                    break;
                case 4: // 四舍五入，保留 1 位
                    $price = number_format($price, 2, '.', '');
                    break;
                case 5: // 先四舍五入，不保留小数
                    $price = round($price);
                    break;
            }
        } else {
            if (!$price) {
                $price = 0;
            }

            $price =  intval($price);
        }

        return sprintf("%s 积分", $price);
    }

    public function get_image_path($goods_id, $image = '', $thumb = false, $call = 'goods', $del = false)
    {
        //$this->_CFG['no_picture'] = ""
        $url = empty($image) ? "" : $image;
        return $url;
    }

    /**
     * 获取折扣
     */
    public function get_discount()
    {
        //如果是五折卡用户切商品不支持五折的，查询会员折扣
        $discount_temp = $GLOBALS['discount'];

        //17 18 19 20会员日  20号卡折扣改了
        $d = date("m-d", time());

        if (($d == '06-18' || $d == '06-19') && $discount_temp >= 0.8) {
            $discount_temp = $discount_temp * 0.88;
        } //会员日8折以上客户参与折上折;

        if (is_hei_wu_date()) {
            $discount_temp = 0.5;
        }
        if (is_66()) {
            $discount_temp = 0.66;
        }
        return $discount_temp;
    }

    /**
     * 获取商品最终价格
     *
     * @access  public
     * @param   int $gsup_id 商品id
     * @param   int $u_price 商品会员价格
     * @param   int $market_price 商品原价
     * @param   array $attr_id 商品规格
     * @param   int $app_rate app折扣
     * @param   int $marketing_price 营销活动金额
     * @param   int $is_vip 不参加任何折扣
     * @return float  产品最终价格
     */
    public function get_goods_final_price($gsup_id = 0, $u_price = 0, $market_price = 0, $attr_id = [], $app_rate = 0, $marketing_price = 0, $is_vip = 0)
    {
        if ($marketing_price) {
            return round($marketing_price, 2);
        }

        if($is_vip){
            $discount_temp = 1;
        }else{
            $discount_temp = $GLOBALS['discount'];
        }

        $u_price = $market_price * $discount_temp;
        //这里取产品价格//搜索暂时无用
        if ($attr_id) {
            rsort($attr_id);
            $goods_attr = implode("|", $attr_id);
            $product_price = $this->name('products')->where('goods_attr_sou', $goods_attr)->value('price');

            if ($product_price) {
                $u_price = $product_price * $discount_temp;//取
            }
        }

        //取促销价格
//        $time = time();
//
//        $where = [
//            ['gsup_id', '=', $gsup_id],
//            ['beg_time', '<=', $time],
//            ['end_time', '>=', $time],
//        ];
//        $promote_price = $this->name('promote')->where($where)->order('price asc')->value('price');
//
//        if ($promote_price) {
//
//            $p_price = $promote_price;
//
//            //存在属性价格，促销价按照属性价格乘比例来折算
//            if (isset($product_price)) {
//                $p_price = $product_price * ($promote_price / $market_price);
//            }
//
//        }


        if (isset($p_price)) {
            $price = $p_price >= $u_price ? $u_price : $p_price;
        } else {
            $price = $u_price;
        }

        $price = round($price, 2);

        return $price;

    }

    //获取商品价格范围
    public function get_range_price($gsup_id, $u_price, $dj_price)
    {


        //取促销价格
        if (empty($p_price)) {

            $time = time();
            $where = [
                ['gsup_id', '=', $gsup_id],
                ['beg_time', '<=', $time],
                ['end_time', '>=', $time]
            ];
            $p_price = $this->name('promote')->where($where)->value('price');
        }

        if (isset($p_price)) {
            if ($p_price >= $u_price) {
                $price = $u_price;
            } else {
                $price = $p_price;
            }
        } else {
            $price = $u_price;
        }

        $price_array_y = array();
        $price_array_y[] = $price;

        $products = $this->name('products')->where('goods_id', '=', $gsup_id)->column('price');

        if (!empty($products)) {
            $price_array_y = array_merge($price_array_y, $products);
        }

        //获取预售折扣 并没有设置
        $presales = $this->name('goods_presale')->where([['goods_id', '=', $gsup_id], ['stock', '>', 0]])->select();
        if (!empty($presales)) {
            foreach ($presales as $pre) {

                if ($pre['discount'] != 0) {

                    $presales_price = $pre['discount'] * $price;
                    $price_array_y[] = $presales_price;
                }
            }
        }

        if ($dj_price) {
            $price_array_y[] = $dj_price;
        }

        $back = array(
            'max_price' => round(max($price_array_y), 2),
            'min_price' => round(min($price_array_y), 2)
        );

        return $back;
    }

    public function recalculate_price($user_id = '', $once = 0)
    {

        if (!$user_id) {
            $user_id = $GLOBALS['user_id'];
            
        }

        /* 取得有可能改变价格的商品：除配件和赠品之外的商品 */
        $field = 'c.rec_id, c.goods_id,c.goods_name,c.presale_id,c.product_id,c.goods_attr_id,c.goods_price, c.goods_number,c.weight,c.is_dingjin,c.shouwan,c.goods_sn,c.is_group,';
        $field .= 'sup.is_sale,sup.name,sup.id as gsup_id,sup.is_you,sup.weight,sup.stock,sup.code,';
        $field .= 'sup.price as market_price,sup.app_rate,if(vs_id != 0, sup.price, sup.price * ' . $GLOBALS['discount'] . ') AS u_price,sup.vs_id';

        if ($once) {
            $db = $this->name('once_cart');
        } else {
            $db = $this->name('cart');
        }

        $res = $db->alias('c')->field($field)
            ->leftJoin('ecs_goods_shp shp', 'shp.id = c.goods_id')
            ->leftJoin('ecs_goods_sup sup', 'sup.id = shp.goods_id')
            ->where('user_id', $user_id)->select();


        foreach ($res AS $row) {

            $marketing_price = 0;
            if ($row['gsup_id'] === null) {
                $this->name('cart')->where('rec_id', $row['rec_id'])->delete();
                continue;
            }
            if ($row['is_sale'] === 2) {
                $this->name('cart')->where('rec_id', $row['rec_id'])->delete();
                continue;
            }
            $update = [];
            $attr_id = empty($row['goods_attr_id']) ? array() : explode(',', $row['goods_attr_id']);


            $goods_price = $this->get_goods_final_price($row['gsup_id'], $row['u_price'], $row['market_price'], $attr_id, $row['app_rate'], $marketing_price, $row['vs_id']);

            /*-如果购物车中存在定金商品且还未开始  则删除这件商品-*/


            if ($row['product_id']) {
                $pro_info = $this->name('products')->field('stock,weight,product_sn')->where('product_id', $row['product_id'])->find();
                if(!$pro_info){
                    $this->name('cart')->where('rec_id', $row['rec_id'])->delete();
                    continue;
                }
                $stock = $pro_info['stock'];
                $weight = $pro_info['weight'];
                $code = $pro_info['product_sn'];
            } else {
                $stock = $row['stock'];
                $weight = $row['weight'];
                $code = $row['code'];
            }


            if (!$stock || $row['is_sale'] == 1) {
                if ($row['shouwan'] == 0) {
                    $update['shouwan'] = 1;
//                    $update['checked'] = 0;      //列表刷新不自动去除购物车勾选
                }

            } else {
                if ($row['shouwan'] == 1) {
                    $update['shouwan'] = 0;
                }
            }

            if ($row['goods_name'] != $row['name']) {
                $update['goods_name'] = $row['name'];
            }

            /*-商品大于库存时则置数量为1-*/
            if ($row['goods_number'] > $stock) {
                $update['goods_number'] = 1;
            }

            if ($row['weight'] != $weight) {
                $update['weight'] = $weight;
            }
            //更新购物车条码

            if ($row['goods_sn'] != $code) {
                $update['goods_sn'] = $code;
            }

            if ($row['goods_price'] != $goods_price) {
                $update['goods_price'] = $goods_price;
            }

            $where = [
                ['rec_id', '=', $row['rec_id']]
            ];

            if (count($update) > 0) {
                if ($once) {
                    $db = $this->name('once_cart');
                } else {
                    $db = $this->name('cart');
                }
                $db->where($where)->update($update);
            }
        }


    }

    public function get_weight_goods($goods_id, $product_id)
    {
        if (intval($product_id) > 0) {
            $weight_p = $this->name('products')->where('product_id', $product_id)->value('weight');

            if ($weight_p) {
                return $weight_p;
            }
        }
        $weight = $this->name('goods_sup')->where('id', $goods_id)->value('weight');
        return $weight;

    }

    public function get_goods_attr_info($arr)
    {
        $attr = '';

        if (!empty($arr)) {
            $arr_ids = implode(',', $arr);
            $fmt = "%s:%s[%s] \n";
//            $number= count($arr);
//            for($i=0;$i<=$number;$i++) {
//                $id=$arr_ids[$i]['id']
//            }
            $sql = "SELECT a.attr_name, ga.attr_value, ga.attr_price " .
                "FROM ecs_goods_attr  AS ga, " .
                "ecs_attribute AS a " .
                "WHERE ga.goods_attr_id in ($arr_ids) AND a.attr_id = ga.attr_id";
            $res = $this->query($sql);
            if ($res) {
                foreach ($res as $row) {
                    $attr_price = round(floatval($row['attr_price']), 2);
                    $attr .= sprintf($fmt, $row['attr_name'], $row['attr_value'], $attr_price);
                }
            }
            $attr = str_replace('[0]', '', $attr);
        }

        return $attr;
    }

    //获取多买优惠的价格等信息
    function get_duomaiyouhui($goods_id, $number)
    {
        $ratio = 1;
        $where = [
            ['goods_id', '=', $goods_id],
            ['num', '<=', $number]
        ];

        $ratio2 = $this->name('goods_muchprice')->where($where)->order('num desc')->limit(1)->value('ratio');
        if ($ratio2) {
            return $ratio2;
        }
        return $ratio;

    }

    public function get_goodsn_goods($goods_id, $product_id)
    {
        if (intval($product_id) > 0) {
            $product_sn = $this->name('products')->where('product_id', $product_id)->value('product_sn');

            if ($product_sn) {
                return $product_sn;
            }

        }

        $code = $this->name('goods_sup')->where('id', $goods_id)->value('code');
        return $code;

    }

    public function get_gsup_id($gshp_id = 0)
    {
        return $this->name('goods_shp')->where('id', $gshp_id)->value('goods_id');
    }

    public function get_gsup($gshp_id = 0)
    {
        if (!empty($gshp_id)) {

            $info = $this->name('goods_sup')->alias('sup')->join('ecs_goods_shp shp', 'sup.id = shp.goods_id')->where('shp.id', $gshp_id)->find();
            return $info;
        } else {
            $list = $this->name('goods_sup')->alias('sup')->where('is_delete', 0)->select();;
            return $list;
        }
    }


    public function check_limit_buy($gsup_id)
    {

        $where = [
            ['goods_id', '=', $gsup_id],
            ['start_time', '<=', time()],
            ['end_time', '>=', time()]
        ];
        $res = $this->name('goods_limit')->where($where)->value('number');
        $number = $res ? $res : 0;
        return $number;
    }
}