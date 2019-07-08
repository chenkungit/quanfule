<?php
namespace app\dashboard\model;

use think\Model;
use think\Request;

class Sup extends Model
{
    protected $table = 'ecs_goods_sup';

    protected $arr =[
        'item'=>null,
        'pagecount'=>1,
        'count'=>0
    ];
    protected $request;
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->request = Request()->param();
    }

    public function get_sup_list(){
        $page = isset($this->request['page'])?  $this->request['page'] : 1;
        $limit = isset($this->request['limit'])?  $this->request['limit'] : 40;
        if(isset($this->request['category'])) $where[] = ['sup.category','=',intval($this->request['category'])];
        if(isset($this->request['brand'])) $where[] =['sup.brand','=',intval($this->request['brand'])];
        if(isset($this->request['keywords'])) $where[] = ['sup.name','like',"%".$this->request['keywords']."%"];
        if(isset($this->request['code'])) $where[] = ['sup.code','like',"%".$this->request['code']."%"];
        if(isset($this->request['is_dingjin'])) $where[] = ['sup.is_dingjin','=',intval($this->request['is_dingjin'])];
        if(isset($this->request['is_sale'])) $where[] = ['sup.is_sale','=',intval($this->request['is_sale'])];
        if(isset($this->request['supplier'])) $where[] = ['sup.supplier','=',intval($this->request['supplier'])];
        if(isset($this->request['is_nei'])) $where[] =  ['sup.is_nei','=',intval($this->request['is_nei'])];


        $where[] = ['sup.is_delete','=',0];
        $join = [
            ['ecs_brand b','b.brand_id = sup.brand','left'],
            ['ecs_category c','c.cat_id = sup.category','left'],
            ['ecs_suppliers s','s.id = sup.supplier','left']
        ];

        $res = $this->name('goods_sup')->alias('sup')->field("sup.*,s.name as suppliers_name,c.cat_name,b.brand_name")
                ->where($where)->join($join)->order('add_time DESC')->page($page,$limit)->select();
        foreach ($res as &$item){

            $presale = $this->table('ecs_goods_presale')->where('goods_id',$item['id'])->select();
            $item['pre_stock'] = 0;
            if(count($presale)){
                $item['is_presale'] = 1;
                $item['pre_stock'] = $this->table('ecs_goods_presale')->where('goods_id',$item['id'])->sum('stock');
            }
        }
        $this->arr['item'] = $res;
        $this->arr['count'] = $this->name('goods_sup')->alias('sup')->where($where)->count();
        $this->arr['pagecount'] = ceil(($this->arr['count']/$limit));

        return $this->arr;
    }

    public function sup_info($goods_id){
        return $this->where('id',$goods_id)->find();
    }

}