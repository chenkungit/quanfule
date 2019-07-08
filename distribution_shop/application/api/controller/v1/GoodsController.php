<?php

namespace app\api\controller\v1;

use app\common\controller\ApiController;
use app\common\model\Dj;
use app\common\model\Favourable;
use app\common\model\Flash;
use app\common\model\Crowdfunding;
use app\common\model\Freeship;
use app\common\model\Goods;
use app\common\model\Product;
use app\common\model\Present;
use app\common\model\Water;
use Qiniu\Config;
use think\Request;
use think\Db;

class GoodsController extends ApiController
{
    protected $goods;
    protected $product;

    public function __construct(Request $request, Goods $goods, Product $product)
    {
        parent::__construct($request);
        $this->goods = $goods;
        $this->product = $product;
        $this->checkBasicAuth();
    }

    public function info()
    {

        $gshp_id = isset($this->data['gshp_id']) ? $this->data['gshp_id'] : 0;
        $gsup_id = isset($this->data['gsup_id']) ? $this->data['gsup_id'] : 0;
        $is_group = isset($this->data['is_group']) ? $this->data['is_group'] : 0;
        $code = isset($this->data['code']) ? $this->data['code'] : 0;

        $goods_basic = $this->goods->get_goods_info($gshp_id, $code, $gsup_id, $is_group);


        $goods_basic['is_remind'] = 0;

        $goods_basic['properties'] = $this->goods->get_goods_properties($gshp_id, $code, $gsup_id);

        $goods_basic['shouhou'] = config('others.shouhou');

        $this->goods->click($gshp_id);

        return $this->respondWithArray($goods_basic);
    }

    public function price($gsup_id, $attr_id = [], $number = 1, $presale = 0, $is_group = 99)
    {

        $validate = $this->validate($this->data, 'app\api\validate\GoodsValidate.price');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
        $marketing_price = 0;
        $field = 'sup.id as gsup_id,is_you,vs_id,stock,sup.price as market_price,if(vs_id != 0, sup.price, sup.price * ' . $GLOBALS['discount'] . ') AS price,start_num,Nbei,marketing_type';

        $row = $this->goods->name('goods_sup')->field($field)->alias('sup')->where('sup.id', $gsup_id)->find();

        $res['stock'] = $row['stock'];

        if (!empty($row['start_num']) && $number < $row['start_num']) {
            return json_error('', '起订量为' . $row['start_num'] . '以上', self::INVALID_PARAMETER);
        }
        if (!empty($arr['Nbei'])) {
            if ($number % $arr['Nbei'] != 0) {
                return json_error('', '起订量为' . $arr['Nbei'] . '的倍数', self::INVALID_PARAMETER);
            }
        }

        /*-处理数据格式-*/
//        $presale_arr =explode(',',$presale);


        if ($this->goods->name('products')->where('goods_id', $gsup_id)->find()) {
            $res['stock'] = $this->goods->name('products')->where('goods_id', $gsup_id)->sum('stock');
            if (count($attr_id) >= 1) {
                $goods_attr = $this->goods->goods_attr_sort($attr_id);
                $attr = $this->goods->name('products')->where([['goods_id', '=', $gsup_id], ['goods_attr_sou', '=', $goods_attr]])->find();
                if ($attr) {
                    $res['stock'] = $attr['stock'];
                }
            }
        }
        $presale = 0;


        $app_price = $this->goods->get_goods_final_price($gsup_id, $row['price'], $row['market_price'], $attr_id, 0, $marketing_price, $row['vs_id']);  //APP价格

        $res['result_danjia'] = $this->goods->price_format($app_price);
        $res['result'] = $this->goods->price_format($app_price * $number);
        $res['no_allow_attr_arr'] = $this->no_allow_att_arr($gsup_id, $attr_id, $presale);


        return $this->respondWithArray($res);

    }

    private function no_allow_att_arr($gsup_id, $attr_id, $presale_id)
    {

        /*-处理数据格式-*/
        if ($presale_id == 0) {
            $presale_arr = [];
        } else {
            $presale_arr = explode(',', $presale_id);
        }

        //判断属性组合以及预售期来确定前台可选属性
        $no_allow_attr_arr = array();//禁止选取的属性数组


        $goods_attr_id_arr = $this->goods->name('goods_attr')->where('goods_id', $gsup_id)->column('goods_attr_id');
        if (!empty($goods_attr_id_arr)) {
            $goods_attr_arr = $this->goods->name('products')->where([['stock', '>', 0], ['goods_id', '=', $gsup_id]])->column('goods_attr');
            if (!empty($goods_attr_arr)) {
                $goods_attr_str = implode("|", $goods_attr_arr);
                $goods_attr_array = explode("|", $goods_attr_str);
                $no_allow_attr_arr = array_diff($goods_attr_id_arr, $goods_attr_array);
            }
        }


        //已经选取了某些属性
        if (!empty($attr_id)) {

            foreach ($attr_id as $key => $val) {
                //属性分类id

                $attr = $this->goods->name('goods_attr')->where([['goods_attr_id', '=', $val], ['goods_id', '=', $gsup_id]])->value('attr_id');

                //查找该商品的其他属性分类
                if (!empty($attr))
                    $goods_attr_id_arr = $this->goods->name('goods_attr')->where([['attr_id', 'neq', $attr], ['goods_id', '=', $gsup_id]])->column('goods_attr_id');

                if (empty($goods_attr_id_arr)) {
                    continue;
                }

                //查询产品/属性的合并
                if (empty($presale_arr)) {

                    $products_list = $this->goods->name('products')->where([['stock', '>', 0], ['goods_id', '=', $gsup_id]])->select();
                } else {

                    $where = [
                        ['gp.stock', '>', 0],
                        ['gp.id', 'in', $presale_arr],
                    ];

                    $products_list = $this->goods->name('products')->alias('p')->field('p.*')->where($where)->select();

                }

                foreach ($products_list as $k => $v) {
                    $goods_attr_arr = explode("|", $v['goods_attr']);
                    if (in_array($val, $goods_attr_arr)) {
                        $goods_attr_id_arr = array_diff($goods_attr_id_arr, $goods_attr_arr);
                    }
                }
                $no_allow_attr_arr = array_merge($no_allow_attr_arr, $goods_attr_id_arr);

            }

        }

        $no_allow_attr_arr = array_values(array_unique($no_allow_attr_arr));

        return $no_allow_attr_arr;
    }

    public function remind()
    {
        $this->checkBasicAuth(1);

        try {
            if ($this->request->isDelete()) {

                $validate = $this->validate($this->data, 'app\api\validate\GoodsValidate.cancle_remind');
                if ($validate !== true) {
                    return json_error(NULL, $validate, self::INVALID_PARAMETER);
                }
                Db::name('goods_remind')->where([['user_id', '=', $GLOBALS['user_id']], ['gsup_id', '=', $this->data['gsup_id']]])->delete();
                return $this->respondWithArray(['is_remind' => 0], '提醒已取消');
            } else {
                $validate = $this->validate($this->data, 'app\api\validate\GoodsValidate.remind');
                if ($validate !== true) {
                    return json_error(NULL, $validate, self::INVALID_PARAMETER);
                }
                $label = Db::name('goods_sup')->where('id', $this->data['gsup_id'])->value('shortname');
                Db::name('goods_remind')->insert(['mobile' => $this->data['mobile'], 'gsup_id' => $this->data['gsup_id'], 'label' => $label, 'user_id' => $GLOBALS['user_id']]);
                return $this->respondWithArray(['is_remind' => 1], '设置提醒成功');
            }
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}