<?php

namespace app\api\controller\v2;

use app\common\controller\ApiController;
use app\common\Enums\RedisKeyEnums;
use app\common\model\v2\Ad;
use app\dashboard\model\Nav;
use app\dashboard\model\Android;
use app\dashboard\model\Ios;
use think\Db;
use think\Request;
use app\common\model\Goods;

class HomeController extends ApiController
{
    protected $Nav;
    protected $Ad;
    protected $Android;
    protected $Ios;

    public function __construct(Request $request, Nav $nav, Ad $ad,Android $android,Ios $ios)
    {
        parent::__construct($request);
        $this->Nav = $nav;
        $this->Ad = $ad;
        $this->Android = $android;
        $this->Ios = $ios;
        $this->checkBasicAuth();
    }

    /*-v2接口 首页做分页 默认返回3个主题-*/
    public function index(Goods $goods)
    {

        $type = isset($this->data['type']) ? $this->data['type'] : 1;
        $page = isset($this->data['page']) ? intval($this->data['page']) : 1;
        $limit = isset($this->data['limit']) ? intval($this->data['limit']) : 3;
        $pagetype = isset($this->data['pagetype']) ? $this->data['pagetype'] : 1;

        $cache_index = sprintf(RedisKeyEnums::CACHE_INDEX, $type, $GLOBALS['discount'], $page, $limit,$pagetype,2);
        if (!($arr = CacheGet($cache_index))) {

            if ($page > 1||$pagetype==2) goto theme;
            $arr['nav'] = [];
            $arr['carousel'] = [];
            $arr['four_banner'] = [];
            $arr['toutiao'] = [];
            $arr['middle_banner'] = [];
            $arr['jrdp'] = [];
            $arr['theme'] = [];
            $arr['rank'] = [];
            $arr['startup'] = [];
            $arr['pop_up'] = [];
            $arr['suspend'] = [];
            $arr['community'] = [];
            $arr['community_name'] = '社区精选';
            $arr['new_user'] = [];
            $arr['gardening'] = [];
            $arr['gardening_name'] = '园艺世界';
            $arr['gardening_btn'] = false;
            $arr['appVersion'] = [];
            /*-导航栏start-*/
            $res = $this->Nav->where('enabled', 1)->order('sort asc')->select()->toArray();
            $arr['nav'] = $res;
            /*-导航栏end-*/

            /*-广告start-*/
            $where = [
                ['carousel_type', '=', $type],
                ['enabled', '=', 1]
            ];

            $res = $this->Ad->name('carousel')->field('sort,carousel_position,carousel_name,redirect_type,redirect_id,' . concat_img('img_url', '', ''))
                ->where($where)
                ->whereTime('add_time', '<=', time())
                ->whereTime('end_time', '>=', time())
                ->order('sort asc')->select();

            foreach ($res as $item) {
                if ($item['carousel_position'] == 0) {
                    $arr['carousel'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 1) {
                    $arr['four_banner'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 2) {
                    $arr['toutiao'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 3) {
                    $arr['middle_banner'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 4) {
                    $arr['jrdp'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 5) {
                    $arr['startup'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 6) {
                    $arr['pop_up'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 7) {
                    $arr['suspend'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 8) {
                    $arr['community'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 9) {
                    $arr['new_user'][] = $item;
                    continue;
                }
                if ($item['carousel_position'] == 10) {
                    $arr['gardening'][] = $item;
                    continue;
                }
            }
            $middle_banner=[];
            foreach ($arr['middle_banner'] as $val){
                if($val['sort']<100){
                    $middle_banner[]=$val;
                }
            }
            $arr['middle_banner']=$middle_banner;
            /*-广告end-*/
            theme:
            if ($pagetype==2) goto rank;

            $arr['theme'] = $this->Ad->theme_show($type, $page, $limit,2);


            rank:

            if($pagetype==2){
                $arr['rank']=[];
                $sup_ids = Db::name('goods_rank')->alias('gr')->where(function ($query){
                    if(isset($this->data['category'])){
                        $query->where('gr.category',$this->data['category']);
                    }
                })->where('sup.is_sale','in',[0,2])->where('curdate',date('Y-m-d'))->leftJoin('ecs_goods_sup sup','gr.sup_id = sup.id')->order('sum_number desc')->page($page,$limit)->column('sup_id');
                if(count($sup_ids)) {
                    $g_arr = [];
                    $good_info = $goods->get_goods_content($sup_ids);

                    if ($good_info) {
                        $g_arr = array_merge($g_arr, $good_info);
                    }

                    if (count($g_arr)) {
                        $arr['rank'] = $g_arr;
                    }
                }
            }


            remember($cache_index, $arr, rand(3600, 7200));
        }
        /*-商品主题end-*/

        return $this->respondWithArray($arr);
    }
}