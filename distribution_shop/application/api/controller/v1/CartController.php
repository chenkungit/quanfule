<?php

namespace app\api\controller\v1;


use app\common\controller\ApiController;
use app\common\model\Cart;
use app\common\model\Crowdfunding;
use app\common\model\Dj;
use app\common\model\Goods;
use think\Db;
use think\Request;

class CartController extends ApiController
{
    protected $cart;

    public function __construct(Request $request, Cart $cart)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
        $this->cart = $cart;
    }

    public function lists()
    {

        $this->cart->recalculate_price();

        $res = $this->cart->get_cart_goods_all();

        return $this->respondWithArray($res);
    }

    public function add(Goods $goods)
    {

        /*-处理数据格式-*/
        $this->data['presale'] = empty($this->data['presale']) ? 0 : explode(',', $this->data['presale']);
        $this->data['is_dingjin'] = empty($this->data['is_dingjin']) ? 0 : $this->data['is_dingjin'];
        $this->data['attr_id'] = empty($this->data['attr_id']) ? [] : $this->data['attr_id'];
        $this->data['delivery_id'] = empty($this->data['delivery_id']) ? '' : $this->data['delivery_id'];
        $this->data['libao'] = empty($this->data['libao']) ? 0 : $this->data['libao'];

        $validate = $this->validate($this->data, 'app\api\validate\CartValidate.add');

        if ($validate !== true) {
            return json_error('', $validate, static::INVALID_PARAMETER);
        }
        $main_goods_info = $goods->main_goods_info($this->data['gshp_id']);

        if (empty($main_goods_info)) {
            abort(404, '该商品不存在');
        }
        $gsup_id = $main_goods_info['gsup_id'];

        if (!empty($main_goods_info['start_num']) && $this->data['number'] < $main_goods_info['start_num']) {
            return json_error('', '起订量为' . $main_goods_info['start_num'] . '以上');
        }
        if (!empty($main_goods_info['Nbei'])) {
            if ($this->data['number'] % $main_goods_info['Nbei'] != 0) {
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
            $limit_number = $goods->check_limit_buy($main_goods_info['gsup_id']);
            if ($limit_number) {
                if ($this->data['number'] > $limit_number) {
                    return json_error('', '超过限购数量');
                }
            }
        }
        $marketing_price = 0;

        $presale = 0;

        $product_arr = $this->cart->name('products')->where('goods_id', $gsup_id)->select();
        if (count($product_arr) > 0) {
            if (empty($this->data['attr_id']) || !is_array($this->data['attr_id'])) {
                return json_error('', '请选择商品属性', self::INVALID_PARAMETER);
            } else {
                $goods_attr = $goods->goods_attr_sort($this->data['attr_id']);
                $product_info = $this->cart->name('products')->where([['goods_id', '=', $gsup_id], ['goods_attr', '=', $goods_attr]])->find();
                $main_goods_info['stock'] = $product_info['stock']; //库存覆盖大库存
                if (empty($product_info)) {
                    return json_error('', '选择属性不全', self::INVALID_PARAMETER);
                } else if ($product_info['stock'] == 0) {
                    return json_error('', '该商品已售完', self::INVALID_PARAMETER);
                }
            }
        }


        if (!isset($product_info)) {
            $this->data['attr_id'] = [];
            $product_info = array('product_id' => 0, 'goods_attr' => '', 'product_sn' => '', 'stock' => 0);
        }

        if ($main_goods_info['stock'] == 0) {
            return json_error('', '该商品已售完', self::INVALID_PARAMETER);
        }

        $goods_price = $this->cart->get_goods_final_price($gsup_id, $main_goods_info['u_price'], $main_goods_info['market_price'], $this->data['attr_id'], 0, $marketing_price, 0);//使用实际价格

        $weight = $this->cart->get_weight_goods($gsup_id, $product_info['product_id']);

        $goods_attr = $this->cart->get_goods_attr_info($this->data['attr_id']);

        $goods_attr_id = join(',', $this->data['attr_id']);

        /* 初始化要插入购物车的基本件数据 */
        $parent = array(
            'user_id' => $GLOBALS['user_id'],
            'session_id' => $GLOBALS['key'],
            'goods_id' => $this->data['gshp_id'],
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
            'is_dingjin' => $this->data['is_dingjin'],
        );

        /* 检查该商品是否已经存在在购物车中 */
        $where = [
            ['goods_id', '=', $this->data['gshp_id']],
            ['presale_id', '=', $presale],
            ['goods_attr_id', '=', $goods_attr_id],
            ['libao', '=', 0],
            ['is_dingjin', '=', $this->data['is_dingjin']],
            ['user_id', '=', $GLOBALS['user_id']],
        ];
        $row = $this->cart->name('cart')->field('goods_number,rec_id')->where($where)->find();

        if ($row) {
            if (isset($this->data['is_edit'])) {
                if ($row['rec_id'] == $this->data['rec_id']) {//判断商品是否存在购物车内并且是否编辑且编辑的id与购物车id相同
                    $iid = $row['rec_id'];
                    $number = $this->data['number'];
                    if ($number <= $main_goods_info['stock']) {
                        if ($main_goods_info['is_you'] == 1) {
                            $you_ratio = $this->cart->get_duomaiyouhui($gsup_id, $number);
                            $goods_price = $goods_price * $you_ratio;
                        }
                        if ($main_goods_info['is_limit'] == 1) {
                            $limit_number = $goods->check_limit_buy($main_goods_info['gsup_id']);
                            if ($limit_number) {
                                if ($number > $limit_number) {
                                    return json_error('', '超过限购数量');
                                }
                            }
                        }
                        $update = [
                            'goods_number' => $number,
                            'goods_price' => $goods_price,
                            'add_time' => time(),
                            'checked' => 1
                        ];
                        $where = [
                            ['goods_id', '=', $this->data['gshp_id']],
                            ['presale_id', '=', $presale],
                            ['goods_attr_id', '=', $goods_attr_id],
                            ['user_id', '=', $GLOBALS['user_id']],
                        ];

                        Db::name('cart')->where($where)->update($update);

                        //处理买一送一
                        //addto_buyone_giveone($gsup_id, $row['rec_id']);
                        if ($main_goods_info['is_virtual'] > 0)//虚拟商品清楚checked全部为0
                        {
                            $update = [
                                'checked' => 0
                            ];
                            $where = [
                                ['rec_id', 'neq', $iid],
                                ['user_id', '=', $GLOBALS['user_id']]
                            ];
                            $this->cart->name('cart')->where($where)->update($update);
                        }

                    } else {
                        return json_error('', '购买数量超过库存', self::INVALID_PARAMETER);
                    }
                } else {//判断商品是否存在购物车内并且是否编辑且编辑的id与购物车id不相同
                    $number = $this->data['number'];
                    if ($main_goods_info['is_you'] == 1) {
                        $you_ratio = $this->cart->get_duomaiyouhui($gsup_id, $number);
                        $goods_price = $goods_price * $you_ratio;
                    }
                    $parent['goods_price'] = $goods_price;
                    $parent['goods_number'] = $number;
                    $iid = $this->data['rec_id'];
                    $this->cart->where('rec_id', $row['rec_id'])->delete();//删除另一个购物车相同商品
                    $this->cart->save($parent, ['rec_id' => $iid]);

                    if ($this->data['libao'])//礼包商品必须更新礼包id
                    {
                        $update = ['libao' => $iid];
                        Db::name('cart')->where('rec_id', $iid)->update($update);
                    }

                    //处理买一送一
                    //addto_buyone_giveone($gsup_id,$iid);
                    if ($main_goods_info['is_virtual'] > 0)//虚拟商品清楚checked全部为0
                    {
                        $update = [
                            'check' => 0
                        ];
                        $where = [
                            ['rec_id', 'neq', $iid],
                            ['user_id', '=', $GLOBALS['user_id']]
                        ];
                        Db::name('cart')->where($where)->update($update);
                    }
                }
            } else {//存在购物车不编辑走正常添加逻辑
                $iid = $row['rec_id'];
                $number = $this->data['number'] + $row['goods_number'];
                if ($number <= $main_goods_info['stock']) {
                    if ($main_goods_info['is_you'] == 1) {
                        $you_ratio = $this->cart->get_duomaiyouhui($gsup_id, $number);
                        $goods_price = $goods_price * $you_ratio;
                    }
                    if ($main_goods_info['is_limit'] == 1) {
                        $limit_number = $goods->check_limit_buy($main_goods_info['gsup_id']);
                        if ($limit_number) {
                            if ($number > $limit_number) {
                                return json_error('', '超过限购数量');
                            }
                        }
                    }
                    $update = [
                        'goods_number' => $number,
                        'goods_price' => $goods_price,
                        'add_time' => time(),
                        'checked' => 1
                    ];
                    $where = [
                        ['goods_id', '=', $this->data['gshp_id']],
                        ['presale_id', '=', $presale],
                        ['goods_attr_id', '=', $goods_attr_id],
                        ['user_id', '=', $GLOBALS['user_id']],
                    ];

                    Db::name('cart')->where($where)->update($update);

                    //处理买一送一
                    //addto_buyone_giveone($gsup_id, $row['rec_id']);
                    if ($main_goods_info['is_virtual'] > 0)//虚拟商品清楚checked全部为0
                    {
                        $update = [
                            'checked' => 0
                        ];
                        $where = [
                            ['rec_id', 'neq', $iid],
                            ['user_id', '=', $GLOBALS['user_id']]
                        ];
                        $this->cart->name('cart')->where($where)->update($update);
                    }

                } else {
                    return json_error('', '购买数量超过库存', self::INVALID_PARAMETER);
                }
            }

        } else {//不存在购物车

            if (isset($this->data['is_edit'])) {//不存在购物车中进行编辑
                $number = $this->data['number'];
                if ($main_goods_info['is_you'] == 1) {
                    $you_ratio = $this->cart->get_duomaiyouhui($gsup_id, $number);
                    $goods_price = $goods_price * $you_ratio;
                }
                $parent['goods_price'] = $goods_price;
                $parent['goods_number'] = $number;
                $iid = $this->data['rec_id'];
                $this->cart->save($parent, ['rec_id' => $iid]);
                if ($this->data['libao'])//礼包商品必须更新礼包id
                {
                    $update = ['libao' => $iid];
                    Db::name('cart')->where('rec_id', $iid)->update($update);
                }

                //处理买一送一
                //addto_buyone_giveone($gsup_id,$iid);
                if ($main_goods_info['is_virtual'] > 0)//虚拟商品清楚checked全部为0
                {
                    $update = [
                        'check' => 0
                    ];
                    $where = [
                        ['rec_id', 'neq', $iid],
                        ['user_id', '=', $GLOBALS['user_id']]
                    ];
                    Db::name('cart')->where($where)->update($update);
                }

            } else {//不存在购物车中添加
                $number = $this->data['number'];
                if ($main_goods_info['is_you'] == 1) {
                    $you_ratio = $this->cart->get_duomaiyouhui($gsup_id, $number);
                    $goods_price = $goods_price * $you_ratio;
                }

                $parent['goods_price'] = $goods_price;
                $parent['goods_number'] = $number;
                $iid = $this->cart->name('cart')->insertGetId($parent);

                if ($this->data['libao'])//礼包商品必须更新礼包id
                {
                    $update = ['libao' => $iid];
                    Db::name('cart')->where('rec_id', $iid)->update($update);
                }

                //处理买一送一
                //addto_buyone_giveone($gsup_id,$iid);
                if ($main_goods_info['is_virtual'] > 0)//虚拟商品清楚checked全部为0
                {
                    $update = [
                        'check' => 0
                    ];
                    $where = [
                        ['rec_id', 'neq', $iid],
                        ['user_id', '=', $GLOBALS['user_id']]
                    ];
                    Db::name('cart')->where($where)->update($update);
                }
            }

        }

        $count = Db::name('cart')->where('user_id', $GLOBALS['user_id'])->count();
        $sum = Db::name('cart')->where('user_id', $GLOBALS['user_id'])->sum('goods_number');
        return $this->respondWithArray(['count' => $count, 'sum' => $sum], '加入购物车成功');
    }


    public function edit()
    {

        $rec_id = empty($this->data['rec_id']) ? 0 : intval($this->data['rec_id']);
        $number = empty($this->data['number']) ? 0 : intval($this->data['number']);
        $checked_ids_arr = empty($this->data['checked_ids']) ? array() : $this->data['checked_ids'];


        if ($number && $rec_id) {  //数量改变
            $markteting_price = 0;
            $field = 'goods_id, goods_attr_id, product_id, shops_id,weight,is_dingjin, presale_id,buyone_rec_id,is_dingjin';
            $cart_goods = $this->cart->name('cart')->field($field)->where('rec_id', $rec_id)->find();

            $gsup_id = $this->cart->get_gsup_id($cart_goods['goods_id']);
            $product_id = $cart_goods['product_id'];
            $presale_id = $cart_goods['presale_id'];
            $weight = $cart_goods['weight'];


            $fields = 'sup.name as goods_name, sup.stock as goods_number,sup.start_num,sup.Nbei,sup.price as market_price,if(vs_id != 0, sup.price, if(vs_id != 0, sup.price, sup.price * ' . $GLOBALS['discount'] . ') AS u_price,';
            $fields .= 'sup.app_rate,sup.is_fei5zhe,sup.is_limit,sup.vs_id';
            $row = $this->cart->name('goods_sup')->alias('sup')->field($fields)->where('id', $gsup_id)->find();

            if (!empty($row['start_num']) && $number < $row['start_num']) {
                return json_error('', '起订量为' . $row['start_num'] . '以上');
            }
            if (!empty($row['Nbei'])) {
                if ($number % $row['Nbei'] != 0) {
                    return json_error('', '起订量为' . $row['Nbei'] . '的倍数');
                }
            }
            if ($row['is_limit'] == 1) {
                $limit_number = (new Goods())->check_limit_buy($gsup_id);
                if ($limit_number) {
                    if ($number > $limit_number) {
                        return json_error('', '超过限购数量');
                    }
                }
            }
            if (empty($presale_id)) {

                if ($row['goods_number'] < $number) {
                    return json_error('', '购买数量超过库存', self::INVALID_PARAMETER);
                }
                /* 是货品*/
                $cart_goods['product_id'] = trim($cart_goods['product_id']);
                if (!empty($cart_goods['product_id'])) {
                    $product_number = $this->cart->name('products')->where([['goods_id', '=', $gsup_id], ['product_id', '=', $product_id]])->value('stock');
                    if ($product_number < $number) {
                        return json_error('', '购买数量超过库存', self::INVALID_PARAMETER);
                    }
                }
            } else {
                $presale_stock = $this->cart->name('goods_presale')->where('id', $presale_id)->value('stock');
                if ($presale_stock < $number) {
                    return json_error('', '购买数量超过库存', self::INVALID_PARAMETER);
                }
            }

            $attr_id = empty($goods['goods_attr_id']) ? array() : explode(',', $goods['goods_attr_id']);


            $goods_price = $this->cart->get_goods_final_price($gsup_id, $row['u_price'], $row['market_price'], $attr_id, $row['app_rate'], $markteting_price, $row['vs_id']);

            if ($cart_goods['buyone_rec_id'] > 0) {
                return json_error('', '赠品不能修改数量', self::INVALID_PARAMETER);
            }

            $this->cart->name('cart')->where('rec_id', $rec_id)->update(['goods_number' => $number, 'add_time' => time()]);

            $result['rec_id'] = $rec_id;
            $result['goods_number'] = $number;
            $result['goods_price'] = $this->cart->price_format($goods_price);
            $result['goods_subtotal'] = $this->cart->price_format($goods_price * $number);
            $result['goods_weighttotal'] = $weight * $number;

            $result['goods_dj_price'] = $this->cart->price_format($goods_price * $number * config('others.DJ_RATE'));
            $result['goods_wk_price'] = $this->cart->price_format($goods_price * $number * (1 - config('others.DJ_RATE')));

        }

        //全部没有选中
        if (empty($checked_ids_arr)) {
            $this->cart->set_cart_goods_checked();
        } else {
            $this->cart->set_cart_goods_checked($checked_ids_arr);
        }
        $cart_goods = $this->cart->get_cart_goods_all();
        return $this->respondWithArray($cart_goods);
    }


    public function delete($rec_id)
    {

        $validate = $this->validate($this->data, 'app\api\validate\CartValidate.delete');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
        $where[] = ['user_id', '=', $GLOBALS['user_id']];
        if (is_array($rec_id)) {
            $where[] = ['rec_id', 'in', $rec_id];
        } else {
            $where[] = ['rec_id', '=', $rec_id];
        }

        $res = $this->cart->where($where)->column('rec_id');
        if (!$res) {
            return json_error('', '购物车内不存在该物品', self::INVALID_PARAMETER);
        }

        try {
            $this->cart->where('rec_id', 'in', $res)->delete();
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }
        return $this->respondWithArray('', '删除成功');
    }

    public function clean()
    {
        try {
            $this->cart->where('user_id', $GLOBALS['user_id'])->delete();
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }
        return $this->respondWithArray('', '清空购物车成功');
    }


}