<?php
namespace app\dashboard\model;

use think\Request;
use think\Model;
use code;

class Commentuser extends  Model
{
    protected $table = "ecs_comment";

    protected $data;

    protected $arr = [
        'item' => [],
        'pagecount'=>1,
    ];
    public function __construct($data = [])
    {
        parent::__construct($data);

    }

    public function Commentuser_list()
    {

        $this->data = Request()->param();
        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 30;

        if(isset($this->data['content'])) $where[] = ['content','LIKE',"%".trim($this->data['content'])."%"];

        if(isset($this->data['id_value'])) $where[] = ['id_value','=',trim($this->data['id_value'])];

        if(isset($this->data['comment_rank'])) $where[] = ['comment_rank','=',intval($this->data['comment_rank'])];

        $where[] = ['c.parent_id','=',0];
        $where[] = ['c.comment_type','=',0];
        $join = [
            ['ecs_goods_shp shp','shp.id= c.id_value','left'],
            ['ecs_goods_sup sup','sup.id = shp.goods_id','left'],
        ];


        $fields = "c.comment_id,c.user_name,c.comment_rank,FROM_UNIXTIME(c.add_time,'%Y-%m-%d %H:%i:%s') as add_time,c.content,sup.name as title,c.goods_attr,c.imgs,c.status";

        $res = $this->name('comment')->alias('c')->field($fields)->join($join)->where($where)->page("$page,$limit")->order('c.add_time desc')->select();

        $count = $this->name('comment')->alias('c')->where($where)->count();

        foreach ($res as $key => &$val) {
            $val['user_name'] = preg_replace("/<(span.*?)>(.*?)<(\/span.*?)>/si","",$res[$key]['user_name']);
            $val['imgs'] = $val['imgs'] ? array_map(function($val){ return trim($val);},explode(',',$val['imgs'])) : null;
            $val['goods_attr'] = $val['goods_attr'] ? str_replace("\n"," ",$val['goods_attr']) : null;
            $val['official_reply'] = $this->name('comment')->where([['parent_id','=',$val['comment_id']],['is_official','=',1]])->find() ?: 0;
        }

        $this->arr['item'] = $res;
        $this->arr['pagecount'] = ceil($count / $limit);

        return $this->arr;
    }

    public function info($comment_id)
    {
        $this->data = Request()->param();
        $page = isset($this->data['page']) ? $this->data['page'] : 1;
        $limit = isset($this->data['limit']) ? $this->data['limit'] : 30;


        $join = [
            ['ecs_comment c2','c1.to_fd = c2.comment_id','left']
        ];
        $fields = "c1.comment_id,c1.parent_id,FROM_UNIXTIME(c1.add_time,'%Y-%m-%d %H:%i:%s') as add_time,c1.imgs,c1.status,c1.user_name,c1.is_official,c1.is_twice,c1.content,c1.user_id,c1.to_fd,c2.user_name as to_user_name";

        $this->arr['item']      = $this->name('comment')->alias('c1')->field($fields)->join($join)->where('c1.parent_id',$comment_id)->order('add_time desc')->page("$page,$limit")->select();
        $this->arr['pagecount'] = ceil($this->name('comment')->where('comment_id',$comment_id)->count()/$limit);

        return $this->arr;
    }

}



