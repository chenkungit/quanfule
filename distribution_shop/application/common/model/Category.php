<?php

namespace app\common\model;


use app\common\Utils\PriceHelper;
use think\Model;

class Category extends Load
{
    const qinniu_url = '';

//    protected $connection ='db_config1';
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /*
     * @main 分类列表
     */
    public function parent_categories_tree($parent_id, $cat_id)
    {

        $fields = 'cat_id,cat_name,mobile_icon,Bgoodimg,redirect_id,redirect_type';

        if ($cat_id != 0) {
            $parent_id = $this->where('cat_id', $cat_id)->value('parent_id');
        }

        $array = $this->field($fields)->where([['is_show', '=', 1], ['parent_id', '=', $parent_id]])->order('sort_order ASC, cat_id ASC')->select();
        $res = [];
        foreach ($array as $key => $value) {
            $res[$key]['cat_id'] = $value['cat_id'];
            $res[$key]['cat_name'] = $value['cat_name'];
            $res[$key]['mobile_icon'] = $value['mobile_icon'] ? build_img_uri($value['mobile_icon']) : '';
            $res[$key]['banner'] = [
                'img_url' => empty($value['Bgoodimg']) ? null : $value['Bgoodimg'],
                'redirect_id' => $value['redirect_id'],
                'redirect_type' => $value['redirect_type']
            ];
        }
        if ($parent_id) {
            $img = $this->field($fields)->where([['cat_id', '=', $parent_id], ['parent_id', '=', 0]])->value('mobile_icon');
            $img = $img ? build_img_uri($img) : '';
            $arr = array('img_url' => null,
                'redirect_id' => '',
                'redirect_type' => 0);
            array_unshift($res, ['cat_id' => (int)$parent_id, 'cat_name' => '全部', 'mobile_icon' => $img, 'banner' => $arr]);
        }
//        if($parent_id==0){
//            array_unshift($array,['cat_id'=>0,'cat_name'=>'全部分类','mobile_icon'=>'','parent_id'=>0]);
//        }
        return $res;
    }


    public function product_list($cat_id, $brand, $beon, $filter, $sort, $order, $page, $limit, $price_range, $format)
    {
        $where = [];
        //查找下级分类
        if ($cat_id != 0) {
            $category = static::cat_list($cat_id);
//        $where = 'shp.is_delete = 0 AND shp.is_sale = 0 AND shp.status = 0 AND sup.is_delete = 0 AND sup.category in('.$category.')';
            $where[] = ['sup.is_delete', '=', 0];
            $where[] = ['sup.category', 'in', $category];
        }

        $price_range = explode(',', $price_range);
        $having = '';
        if (is_array($price_range) && count($price_range) == 2) {
            $having = 'sort_price between ' . min($price_range) . ' and ' . max($price_range);
        }

        if ($brand) {
            $brand_arr = array_unique(explode(',', $brand));
            $length = count($brand_arr);
            if ($length == 1) {
                $where[] = ['sup.brand', '=', $brand];
            } else {
                $where[] = ['sup.brand', 'in', $brand_arr];
            }
        }
        if ($filter) {
            $filter_arr = array_unique(explode(',', $filter));
            $length = count($filter_arr);
            if ($length == 1) {
                $goods_id_arr = $this->name('goods_attr')->where('attr_value', '=', $filter_arr[0])->column('goods_id');
            } else {
                $goods_attr_m = $this->name('goods_attr')->alias('t1');

                $where_tmp = [];
                for ($i = 0; $i < $length - 1; $i++) {
                    $join_alias = 't' . ($i + 2);
                    $goods_attr_m->join("ecs_goods_attr $join_alias", "t1.goods_id=$join_alias.goods_id");
                }
                foreach ($filter_arr as $key => $value) {
                    $where_alias = 't' . ($key + 1);
                    array_push($where_tmp, ["$where_alias.attr_value", '=', $value]);
                }

                $goods_id_arr = $goods_attr_m->where($where_tmp)->column('t1.goods_id');
            }
            if ($goods_id_arr) {
                $where[] = ['sup.id', 'in', $goods_id_arr];
            } else {
                $where[] = ['sup.id', 'in', [0]];
            }
        }

        if ($beon == 3)//即将上架
        {
            $where[] = ['sup.is_sale', '=', 2];
        } else if ($beon == 1)//上架中
        {
            $where[] = ['sup.is_sale', '=', 0];
        } else if ($beon == 4) {
            //*-后台编辑返回-*/
        } else if ($beon == 5) {
            $where[] = ['sup.is_sale', 'in', [0, 2, 3]];
        } else {
            $where[] = ['sup.is_sale', 'in', [0, 2]];
        }
        $sort_order = $this->_goods_list_order($sort, $order);
        $discount = $this->get_discount();
        $field = 'shp.id as shp_id,shp.shops_id,shp.goods_id as sup_id,sup.type,shp.base_sale as sales,shp.add_time,sup.is_sale,shp.base_sale,sup.category,c.cat_name';
        $field .= ',sup.stock,sup.price_temp,sup.icon,sup.ord,sup.name,sup.img,sup.price as market_price,sup.is_fei5zhe,sup.price as sort_price,sup.vs_id';
//        IFNULL(mp.user_price, sup.price * ".$GLOBALS['discount'].")

        $arr = $this->name('goods_shp')->alias('shp')->field($field)
            ->leftJoin('ecs_goods_sup sup', 'shp.goods_id = sup.id')
            ->leftJoin('ecs_category c', 'c.cat_id=sup.category')
            ->where($where)->page("$page,$limit")->having($having)->group('sup.id')->order($sort_order)->select();
        foreach ($arr as $key => &$value) {

            $value['presale'] = 0;
            if (count($this->name('products')->where('goods_id', '=', $value['sup_id'])->select()) > 0) {
                $value['stock'] = $this->name('products')->where('goods_id', '=', $value['sup_id'])->sum('stock');
            }

            $value['thumbnail'] = build_img_uri($arr[$key]['img'], $value['stock'], "", $value['is_sale']);
            $value['is_dingjin'] = 0;

            $value['mark'] = mark($value['is_sale'], $value['presale'], $value['is_dingjin'], $value['stock']);
            $value['after_mark'] = [];
            $price = $this->get_goods_final_price($value['sup_id'], $value['sort_price'], $value['market_price'], [],0,0,$value['vs_id']);

            $arr[$key]['market_price'] = PriceHelper::format($arr[$key]['market_price']);

            $arr[$key]['price'] = PriceHelper::format($price);

        }
        if ($format) {
            $xx = [];
            foreach ($arr as $key => $item) {
                $xx[$item['category']]['cat_name'] = $item['cat_name'];
                $xx[$item['category']]['category'] = $item['category'];
                $xx[$item['category']]['body'][] = $item;
            }
            return array_values($xx);
        } else {
            return $arr;
        }
    }

    public function swiper($cat_id)
    {

        if ($res = $this->name('category')->where('cat_id', $cat_id)->value('swiper')) {
            return json_decode($res, true);
        }
        return null;
    }

    public function filter($cat_id)
    {
        $parent_attr = $this->name('category')->field('filter_attr,parent_id')->where('cat_id', $cat_id)->find();
        /*-如果是大类则返回大类下的所有小类-*/
        $res = [];

        $cat_id_arr = [$cat_id];
        if ($parent_attr['parent_id'] == 0) {
            $res['category']['optional_number'] = 1;
            $res['category']['item'] = $this->where([['is_show', '=', 1], ['parent_id', '=', $cat_id]])->field('cat_id,cat_name')->select()->toArray();
            array_unshift($res['category']['item'], ['cat_id' => $cat_id, 'cat_name' => '全部']);
            $cat_id_arr = array_column($res['category']['item'], 'cat_id');
        }

        $fields = 'b.brand_id, b.brand_name';

        $where = [
            ['sup.category', 'in', $cat_id_arr],
            ['sup.is_sale', '=', 0],
            ['sup.is_delete', '=', 0]
        ];

        $res['brand'] = $this->name('brand')->field($fields)->alias('b')
            ->leftJoin('ecs_goods_sup sup', 'sup.brand = b.brand_id')
            ->where($where)->group('brand_id')->order('b.sort_order, b.brand_id ASC')->select();

        $arr = [];
        $parent_attr_arr = explode(',', $parent_attr['filter_attr']);
        foreach ($parent_attr_arr as $key => $value) {
            $where = [
                ['sup.is_delete', '=', 0],
                ['sup.is_sale', '=', 0],
                ['a.attr_id', '=', $value]
            ];
            $res2 = $this->name('attribute')->field('attr_value,a.attr_name,goods_attr_id')->alias('a')
                ->join('ecs_goods_attr ga', 'a.attr_id = ga.attr_id')
                ->join('ecs_goods_sup sup', 'sup.id = ga.goods_id')
                ->where($where)->group('attr_value')->select();

            if ($res2) {
                foreach ($res2 as $key2 => $value2) {
                    $arr[$key]['attr_name'] = $value2['attr_name'];
                    $arr[$key]['attr_list'][] = [
                        'goods_attr_id' => $value2['goods_attr_id'],
                        'attr_name' => $value2['attr_value']
                    ];
                }
//                array_unshift($arr[$key]['attr_list'],['goods_attr_id'=>0,'attr_name'=>'全部']);
            }
        }
        $res['attr'] = array_values($arr);
        return $res;
    }

    /*-获取分类及其子分类
    * params Number $
    -*/
    public static function cat_list($cat_id)
    {
        $category = $cat_id;

        if (is_array($cat_id)) {
            $children_ids_array = static::where([['is_show', '=', 1], ['parent_id', 'in', $cat_id]])->column('cat_id');
            return array_values(array_unique(array_merge($category, $children_ids_array)));
        } else {
            $children_ids_array = static::where([['is_show', '=', 1], ['parent_id', 'in', $cat_id]])->column('cat_id');
            array_push($children_ids_array, $category);
            return $children_ids_array;
        }
    }


    private function _goods_list_order($sort = 0, $order = 0)
    {

        /*--*/
        $result = "sup.is_sale desc,";


        $sequence = $order ? 'ASC' : 'DESC';

        switch ($sort) {
            //综合排序
            case '0' :
                $result .= 'ord' . ' ' . $sequence;
                $beon_order = " ,shp.new DESC";
                $result .= $beon_order;
                break;
            //销量
            case '1' :
                $result = 'shp.base_sale' . ' ' . $sequence;
                break;
            //价格
            case '2' :
                $result = 'sort_price' . ' ' . $sequence;
                break;
            //上架时间
            case '3':
                $result = 'shp.add_time' . ' ' . $sequence;
                break;
            default:
                $result .= 'ord' . ' ' . $sequence;
                $beon_order = " ,shp.new DESC";
                $result .= $beon_order;
                break;

        }
        return $result;
    }
}