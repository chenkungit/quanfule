<?php
namespace app\api\controller\v1;

use app\common\controller\ApiController;
use think\Db;
use think\Request;
use app\common\model\Search;

class SearchController extends ApiController
{
    private $Search;

    public function __construct(Request $request,Search $search)
    {
        parent::__construct($request);
        $this->checkBasicAuth();
        $this->Search = $search;
    }

    public function sou(){
        $keywords = isset($this->data['keywords']) ? trim($this->data['keywords']) : '';

        $type    = isset($this->data['type']) ? trim($this->data['type']) : 1;
        $presale = empty($this->data['presale']) ? 2:$this->data['presale'];
        $sort    = (isset($this->data['sort'])  && in_array(trim(strtolower($this->data['sort'])), ['sales', 'click_count', 'collects', 'comments', 'sort_price', 'add_time'])) ? trim($this->data['sort'])  : 'sales';
        $order   = (isset($this->data['order']) && in_array(trim(strtoupper($this->data['order'])), ['ASC', 'DESC'] )) ? trim($this->data['order']) : 'DESC';
        $beon    = (isset($this->data['beon']))? intval($this->data['beon']) : 0;
        $page    = (isset($this->data['page']))? intval($this->data['page']) : 1;
        $limit   = (isset($this->data['limit'])) ? intval($this->data['limit']) : 10;

        $key_arr=$this->Search->scws_keywords($keywords);

        $sort_order = sprintf('%s %s',$sort,$order);

        if($type==1)//商品搜索
        {
            $goods = $this->Search->getSearchGoods($key_arr,$presale,$sort_order,$page,$limit,$beon);
//            //现货上架
//            $goods_xh=$this->Search->getSearchGoods($key_arr,1,0);
//            //预售上架
//            $goods_ys=$this->Search->getSearchGoods($key_arr,2,0);
//            //下架
//            $goods_xj=$this->Search->getSearchGoods($key_arr,0,1);

        }

        return $this->respondWithArray($goods);
    }

    /**
     * 搜索ajax事件
     * @param $keyword {关键词}
     * @return array
     */
    public function prompt(){

        $keyword = isset($this->data['keywords']) ? trim($this->data['keywords']) : '';

        $res = $this->Search->name('search_tishi')
                ->where('keyword','like',"%$keyword%")
                ->order('num desc')
                ->limit(10)
                ->select();

        return $this->respondWithArray($res);
    }


    public function hot(){

        $type = isset($this->data['type'])? intval($this->data['type']) : 1;

        $where = [
            ['s_type','=',$type],
            ['enabled','=',1],
        ];

        $res =  Db::name('search_hot')->where($where)->order('sort asc')->select();

        return $this->respondWithArray($res);
    }

    public function history(){
        $this->checkBasicAuth(1);

        $res = Db::name('search_keyword')->field('distinct(keyword)')->where('user_id',$GLOBALS['user_id'])->order('id')->limit(10)->select();

        return $this->respondWithArray($res);
    }



}