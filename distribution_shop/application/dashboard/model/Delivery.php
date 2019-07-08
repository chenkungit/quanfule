<?php


namespace app\dashboard\model;

use app\common\model\Order;
use think\Model;
use think\Request;
use code;

class Delivery extends Model
{
    protected $request;

    public function delivery_list(){
        $this->request = Request()->param();

        $page = isset($this->request['page'])?  $this->request['page'] : 1;
        $limit = isset($this->request['limit'])?  $this->request['limit'] : 15;

        if(!empty($this->request['order_sn'])) $where[] = ['order_sn','LIKE',"%".intval($this->request['order_sn'])."%"];
        if(!empty($this->request['delivery_sn'])) $where[] = ['delivery_sn','LIKE',"%".intval($this->request['delivery_sn'])."%"];
        if(!empty($this->request['consignee'])) $where[] = ['consignee','LIKE',"%".intval($this->request['consignee'])."%"];
        if(!empty($this->request['sendaddress'])) $where[] = ['sendaddress','=',intval($this->request['sendaddress'])];
        if(!empty($this->request['shipping_id'])) $where[] = ['delivery_sn','=',intval($this->request['shipping_id'])];

        $where[] = ['status','=',empty($this->request['status']) ? 0 : intval($this->request['status'])];
        $join = [
            ['ecs_order_info oi','oi.order_id = do.order_id','left']
        ];

        $res = $this->name('delivery_order')->field('do.*,oi.o_status,oi.label')->alias('do')->join($join)->where($where)->page("$page,$limit")->order('update_time desc')->select();
        $count = $this->name('delivery_order')->where($where)->count();
        $pagecount = ceil($count/$limit);
        $order = new Order();
        foreach ($res as &$item){
            $item['add_time']      = date('Y-m-d H:i:s',$item['add_time']);
            $item['shipping_time'] = ( $item['status'] != code::NDS_FINISH_D || empty($value['shipping_time']) )? '' : date('Y-m-d H:i:s',$item['shipping_time']);

            if($item['o_status'] ==  code::NDS_FINISH_D){
                $item['status_name'] = '等待退货处理';
                $item['operate'] = false;
            }else{

                $item['status_name'] = $this->get_dstatus_ch($item['status']);
                $item['operate'] = true;
                $item['remove'] = $item['status'] == code::NDS_WAIT_D ? true : false;
            }

            //处理生成发货单后48小时仍久未发货的异常发货单
            if($item['status']==0&&(time()-strtotime($item['update_time']))>72*3600)
            {
                $item['yichang']= $item['update_time'];
            }
        }
        
        return ['item'=>$res,'page_count'=>$pagecount,'record'=>$count];
    }


    public function delivery_invoice($order_id){
        $res =  $this->name('delivery_invoice')->where('order_id',$order_id)->select();
        foreach ($res as &$item){
            if($item['is_part_sync']){
                $goods_name = $this->name('order_goods')->where('rec_id','in',explode(',',$item['rec_ids']))->column('goods_name');
                $item['rec_ids'] = implode(',',$goods_name);
            }
        }

        return $res;
    }


    //获取发货单中文状态
    function get_dstatus_ch($status){

        if((string)$status == 'all'){
            return array(
                code::NDS_WAIT_D   => '等待发货',
                code::NDS_FINISH_D => '已发货',
                code::NDS_FINISH_R => '已退货',
            );
        }
        switch($status){
            case code::NDS_WAIT_D   : return '等待发货'; break;
            case code::NDS_FINISH_D : return '已发货'; break;
            case code::NDS_FINISH_R : return '已退货'; break;
            default:return '未知状态'; break;
        }

    }

}