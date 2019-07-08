<?php

namespace app\dashboard\controller;

use app\common\controller\Refund;
use app\common\controller\WebController;
use app\common\Enums\RedisKeyEnums;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use app\common\model\Goods;
use app\common\model\Dj;
use app\common\model\Group;
use think\Container;
use think\Db;
use think\Request;
use app\common\model\Search;
use ueditor\ueditorUploader;

class AjaxController extends WebController
{
    protected $goods;

    public function __construct(Request $request, Goods $goods)
    {
        parent::__construct($request);
        $this->goods = new Goods();
    }

    public function search(Goods $goods)
    {

        $keywords = isset($this->data['keywords']) ? $this->data['keywords'] : '';
        $code = isset($this->data['code']) ? $this->data['code'] : '';
        $type = isset($this->data['type']) ? $this->data['type'] : 0;
        $presale = isset($this->data['presale']) ? $this->data['presale'] : 0;

        $limit = 50;
        $where = [];
        if ($type == 1) {
            $db = Db::name('category');
            array_push($where, ['cat_name', 'like', "%$keywords%"]);
        }
        if ($type == 2) {
            $db = Db::name('brand');
            array_push($where, ['brand_name', 'like', "%$keywords%"]);
        }
        if ($type == 3) {
            $db = Db::name('goods_sup');

            if (!empty($code)) {
                array_push($where, ['code', '=', $code]);
            } else {
                $key_arr = Search::scws_keywords($keywords);
                array_push($where, ['name', 'like', $key_arr]);
                array_push($where, ['sup.is_sale', 'in', [0, 2]]);
            }
            $join = [
                ['ecs_goods_shp shp', 'shp.goods_id=sup.id', 'LEFT'],
            ];
            $fields = 'sup.id,shp.id as shp_id,sup.name,sup.price as market_price,shp.base_sale,' . concat_img('sup.thumbnail', 'thumbnail');
//            if($presale==3){
//                array_push($join,['ecs_goods_presale pre','sup.id=pre.goods_id','RIGHT']);
//            }else{
//                array_push($join,['ecs_goods_presale pre','sup.id=pre.goods_id','LEFT']);
//
//                $fields .= ",IF(pre.id,'1','0') as is_presale";
//            }

            $arr = $db->where($where)->field($fields)->alias('sup')->join($join)->group('name')->limit($limit)->select();
            foreach ($arr as &$item) {
                /* 获得商品的销售价格 */
                $item['price'] = $goods->get_goods_final_price($item['id'], $item['market_price'], $item['market_price']);
            }
            return $this->respondWithArray($arr);
        }
        if ($type == 4) {
            $db = Db::name('suppliers');
            array_push($where, ['name', 'like', "%$keywords%"]);
        }
        /*-返回分类下的所有商品-*/
        if ($type == 5) {
            $db = Db::name('goods_sup')->order('id desc');

            array_push($where, ['category', '=', $keywords]);
            array_push($where, ['is_sale', '=', 0]);
        }
        if ($type == 6) {
            $db = Db::name('bonus_type');
            array_push($where, ['type_id', '=', $keywords]);
            $fields = 'type_id,type_name,type_money';
            $db->field($fields);
        }
        if ($type == 7) {
            $db = Db::name('users');
            $fields = 'user_id,user_name,nick,mobile';
            $where = "user_name like '%" . $keywords . "%' or mobile like '%" . $keywords . "%'";
            $db->field($fields);
        }
        if ($type == 8) {
            $db = Db::name('users');
            $fields = 'user_id,user_name,nick,mobile';
            $db->where('mobile', 'in', explode(',', $keywords));
            $db->field($fields);
            $limit = 5000;
        }


        $arr = $db->where($where)->limit($limit)->select();

        return $this->respondWithArray($arr);
    }

    public function search_new()
    {
        $keywords = isset($this->data['keywords']) ? $this->data['keywords'] : '';
        $type = isset($this->data['type']) ? intval($this->data['type']) : 1;
        $where = [];
        switch ($type) {
            case 1:
                $db = Db::name('goods_sup')->alias('sup')->leftJoin('ecs_goods_shp shp', 'sup.id = shp.goods_id')->field('sup.name,sup.id as gsup_id,shp.id as gshp_id');
                array_push($where, ['shp.id', '=', $keywords]);
                array_push($where, ['sup.is_sale', 'in', [0, 2]]);
                break;
            case 2:
                $db = Db::name('category')->field('cat_name as name');
                array_push($where, ['cat_id', '=', $keywords]);
                break;
        }
        $res = $db->where($where)->find();
        if (!$res) {
            return $this->setStatusCode(self::INVALID_PARAMETER)->respondWithArray(null, '不存在');
        }
        return $this->respondWithArray($res);
    }

    public function search_bk()
    {

        $where = [];
        $id = isset($this->data['id']) ? $this->data['id'] : '';
        $type = isset($this->data['type']) ? $this->data['type'] : 0;

        $id = explode(',', $id);

        if ($type == 1) {
            $db = Db::name('category');
            array_push($where, ['cat_id', 'in', $id]);
            $fields = 'cat_id,cat_name';
        }
        if ($type == 2) {
            $db = Db::name('brand');
            array_push($where, ['brand_id', 'in', $id]);
            $fields = 'brand_id,brand_name';
        }
        if ($type == 3) {
            $db = Db::name('goods_sup');
            array_push($where, ['id', 'in', $id]);
            $fields = 'id,shortname';
        }
        if ($type == 4) {
            $db = Db::name('suppliers');
            array_push($where, ['id', 'in', $id]);
            $fields = 'id,name';
        }

        $arr = $db->field($fields)->where($where)->select();

        return $this->respondWithArray($arr);
    }


    public function goods_arr(array $gshp_id_arr = [], array $gsup_id_arr = [])
    {

//        $this->actionAuth();

        $where = [
            ['shp.is_delete', '=', 0],
            ['shp.status', '=', 0],
        ];

//        if(!is_array($gshp_id_arr) || count($gshp_id_arr)==0){
//            throw new HttpException(500,'格式错误');
//        }
        /*-线上商品id-*/
        if ($gshp_id_arr) {
            array_push($where, ['shp.id', 'in', $gshp_id_arr]);
        }
        if ($gsup_id_arr) {
            array_push($where, ['sup.id', 'in', $gsup_id_arr]);
        }


        $join = [
            ['ecs_shops shops', 'shp.shops_id = shops.id', 'LEFT'],
            ['ecs_goods_sup sup', 'shp.goods_id = sup.id', 'LEFT'],
            ['ecs_category cat', 'cat.cat_id = sup.category', 'LEFT'],
            ['ecs_brand b', 'b.brand_id = sup.brand', 'LEFT'],
            ['ecs_suppliers supr', 'supr.id = shp.suppliers_id', 'LEFT'],
            ['ecs_sendaddress send', 'supr.sendaddress=send.id', 'LEFT'],
        ];
        /*sup.img,shp.new,shp.hot,shp.fine,sup.code,,shp.hits,shp.is_freeshipping,sup.icon*/
        $field = 'shp.id as gshp_id,shp.suppliers_id,shp.shops_id,sup.stock,sup.start_num,sup.Nbei,send.address as sendAddress,send.id as sendAddress_id';
        $field .= ',sup.id as gsup_id,sup.name,sup.package,sup.brand,sup.price_temp,sup.img,sup.weight,sup.video_url,sup.video_img,sup.is_you,sup.is_sale,sup.descpt,sup.m_descpt,sup.type,sup.add_time,sup.use_points,sup.start_num,sup.Nbei,';
        $field .= 'sup.app_rate,sup.vs_id,sup.is_miao,sup.tbsm,sup.is_virtual,sup.is_dingjin,sup.is_yuding,sup.rel_articles_id,sup.rel_goods_id,sup.youhui_type,sup.youhui_desc,sup.tishi_desc,sup.shipping_desc,sup.price as market_price';
        $field .= ',cat.measure_unit, cat.cat_name, b.brand_name,shops.name as shops_name';
        $field .= ',if(vs_id != 0, sup.price, sup.price * ' . $GLOBALS['discount'] . ') AS u_price';

        $arr = $this->goods->name('goods_shp')->alias('shp')->field($field)->join($join)->where($where)->select();

        foreach ($arr as &$item) {

            $activities_list = $this->goods->activities_list($item['gsup_id']);
//            $arr['activities_list'] = $activities_list['list'];
            $item['activities_list'] = $activities_list['list'];
            $item['after_mark'] = $activities_list['after_mark'];
            if ($item['after_mark']) {
                $item['after_mark'] = array_slice($activities_list['after_mark'], 0, 2);
            }
            $watermark = $activities_list['watermark'];
            /* 获得商品的销售价格 */
            $price = $this->goods->get_goods_final_price($item['gsup_id'], $item['u_price'], $item['market_price'], [], 0, 0, $item['vs_id']);

            $item['price'] = $this->goods->price_format($price);
//            $item['price1'] = $price;
            $item['market_price'] = $this->goods->price_format($item['market_price']);

            //获取预售信息
            $presale_list = [];

            $presale = $this->goods->name('goods_presale')->where('goods_id', $item['gsup_id'])->select();

            if (count($presale) > 0) {

                $presale_list['has_presale'] = 1;
                $presale_list['presale_stock_sum'] = $this->goods->name('goods_presale')->where('goods_id', $item['gsup_id'])->sum('stock');
                $item['stock'] = $presale_list['presale_stock_sum'];

                foreach ($presale as $key => $val) {
                    //过滤掉已被删除的货品
                    if ($val['product_id']) {
                        $product = $this->goods->name('products')->where('product_id', $val['product_id'])->find();
                        if (empty($product['product_id'])) {
                            continue;
                        }
                    }
                    $tmp[$val['predate']][] = $val['id'];

                }

                foreach ($tmp as $key => $val) {
                    $presale_list['spe'][] = array('pre_id' => implode(",", $val), 'predate' => $key);
                }

            } else {
                $presale_list['has_presale'] = 0;
            }

            $item['thumbnail'] = empty($item['img']) ? '' : build_img_uri($item['img'], $item['stock'], $watermark);
            $item['presale'] = $presale_list;
        }

        return $this->respondWithArray($arr);
    }


    public function upload()
    {
        $this->checkDashBoardAuth();
        $prefix = isset($this->data['prefix']) ? $this->data['prefix'] : 'common';

        if (!isset($_FILES['img'])) {
            return json_error(NULL, '请上传图片', self::INVALID_PARAMETER);
        }
        $upload = UploadFactory::getService(UploadEnums::LOCAL);

        $arr['img_url'] = $upload->singleUpload($_FILES['img'], $prefix, true);

        return $this->respondWithArray($arr);
    }

    public function region()
    {
        $arr = Db::name('region')->column('region_name', 'region_id');
        return $this->respondWithArray($arr);
    }

    public function clearCache()
    {

        $this->actionAuth();

        if (!isset($this->data['arg']) || empty($this->data['arg'])) {
            return json_error(NULL, 'argument为空', self::INVALID_PARAMETER);
        }


        $x = redis()->keys(RedisKeyEnums::PREFIX . $this->data['arg']);

        $flag = redis()->del($x);

        if ($flag === false) {
            return $this->respondWithArray(NULL, '没有可清除的缓存');
        }
        return $this->respondWithArray(NULL, '清除了' . $flag . '个缓存');
    }

    public function order_query(Refund $refund)
    {
        $this->actionAuth();

        $this->_validate('pay_id');
        $refund->setArray($this->data);
        return $this->respondWithArray($refund->common_query($this->data['pay_id']));
    }

    public function refund_order_query(Refund $refund)
    {
        $this->_validate('pay_id');
        $refund->setArray($this->data);
        return $this->respondWithArray($refund->common_refund_query($this->data['pay_id']));
    }

    public function ueditor()
    {

        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(Container::get('app')->getRootPath() . 'extend/ueditor/config.json')), true);
        $action = \request()->param('action');
        $base64 = "upload";
        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;
            case 'uploadimage':
                $config = array(
                    "pathFormat" => $CONFIG['imagePathFormat'],
                    "maxSize" => $CONFIG['imageMaxSize'],
                    "allowFiles" => $CONFIG['imageAllowFiles']
                );
                $fieldName = $CONFIG['imageFieldName'];
                $up = new ueditorUploader($fieldName, $config, $base64);
                $result = json_encode($up->getFileInfo());
                break;
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $CONFIG['scrawlPathFormat'],
                    "maxSize" => $CONFIG['scrawlMaxSize'],
                    "allowFiles" => $CONFIG['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $CONFIG['scrawlFieldName'];
                $base64 = "base64";
                $up = new ueditorUploader($fieldName, $config, $base64);
                $result = json_encode($up->getFileInfo());
                break;
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $CONFIG['videoPathFormat'],
                    "maxSize" => $CONFIG['videoMaxSize'],
                    "allowFiles" => $CONFIG['videoAllowFiles']
                );
                $fieldName = $CONFIG['videoFieldName'];
                $up = new ueditorUploader($fieldName, $config, $base64);
                $result = json_encode($up->getFileInfo());
                break;
            case 'uploadfile':
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }

    }
}