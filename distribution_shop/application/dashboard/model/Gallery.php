<?php
namespace app\dashboard\model;


use think\Model;

class Gallery extends Model
{
    protected $table = 'ecs_goods_gallery';

    public function get_gallery($goods_id):array
    {
        return $this->where('goods_id',$goods_id)->order('sort asc')->select()->toArray();
    }
}