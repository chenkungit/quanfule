<?php
namespace app\dashboard\model;


use think\Model;
use think\Request;
use code;

class Order extends Model
{
    protected $request;
    protected $arr =[
        'item'=>null,
        'pagecount'=>1,
        'count'=>0
    ];

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->request = Request()->param();
    }

    public function order_lists(){

        $page = isset($this->request['page'])   ?  $this->request['page']  : 1;
        $limit = isset($this->request['limit']) ?  $this->request['limit'] : 15;
        if(!empty($this->request['order_id']))     $where[] = ['oi.order_id','=',(int)$this->request['order_id']];
        if(!empty($this->request['order_sn']))     $where[] = ['oi.order_sn','like',"%".trim($this->request['order_sn'])."%"];
        if(!empty($this->request['consignee']))    $where[] = ['oi.consignee','=',$this->request['consignee']];
        if(!empty($this->request['user_name']))    $where[] = ['u.username|u.nick','=',$this->request['user_name']];
        if(!empty($this->request['sendaddress']))    $where[] = ['oi.sendaddress','=',$this->request['sendaddress']];
        if(!empty($this->request['label']))        $where[] = ['oi.label','like',"%".$this->request['label']."%"];
        if(!empty($this->request['o_status']))      $where[] = ['oi.o_status','=',$this->request['o_status']];

        $where[] =['oi.is_delete','=',0] ;

        $fields = "oi.order_id,oi.order_sn,oi.consignee,oi.province,oi.city,oi.district,oi.address,oi.shipping_name,oi.sendaddress,s.address as sendaddress_name,
        FROM_UNIXTIME(add_time,'%Y/%m/%d %H:%i:%s') AS add_time,FROM_UNIXTIME(shipping_time,'%Y/%m/%d %H:%i:%s') AS shipping_time,
        oi.phone,oi.pay_name,oi.referer,IF(u.nick,u.nick,u.mobile) as nick,label,print_info,oi.msg_to_shop
        ,IF(pay_time,FROM_UNIXTIME(pay_time,'%Y/%m/%d %H:%i:%s'),NULL) AS pay_time,(goods_amount + shipping_fee) as order_amount,o_status,order_amount as money_paid";
//        i.c_url
        $join = [
            ['ecs_users u','u.user_id = oi.user_id','left'],
            ['ecs_sendaddress s','oi.sendaddress = s.id','left']
        ];
        $this->arr['item'] = $this->name('order_info')->field($fields)->alias('oi')->join($join)->where($where)->limit($limit)->page($page)->order('add_time DESC')->select();

        foreach ($this->arr['item'] as &$item){
            $item['o_status'] = \code::get_ostatus_ch($item['o_status']);
        }
        $this->arr['count'] = $this->name('order_info')->alias('oi')->where($where)->count();
        $this->arr['pagecount'] = ceil($this->arr['count']/$limit);
        return $this->arr;
    }

    public function order_info($order_id){
        $res = $this->name('order_info')->field('*,(goods_amount - discount + shipping_fee) AS total_fee')->where('order_id',$order_id)->find()->toArray();

        $res['o_status'] = \code::get_ostatus_ch($res['o_status']);
        $res['add_time'] = date('Y-m-d H:i:s',$res['add_time']);
        $res['pay_time'] = date('Y-m-d H:i:s',$res['pay_time']);
        $order_goods = $this->name('order_goods')->alias('og')
                        ->field('og.*,if(`p`.`weight`,`p`.`weight`,`sup`.`weight`)*goods_number as subweight,su.name as suppliers_name,(og.goods_price*og.goods_number-og.discount-og.bonus) as subprice,
                                        gp.predate,if(gp.stock,gp.stock,if(p.stock,p.stock,sup.stock)) as stock')
                        ->leftJoin('ecs_goods_shp shp','og.goods_id = shp.id')
                        ->leftJoin('ecs_goods_sup sup','shp.goods_id = sup.id')
                        ->leftJoin('ecs_products p','p.product_id = og.product_id ')
                        ->leftJoin('ecs_suppliers su','og.suppliers_id = su.id')
                        ->leftJoin('ecs_goods_presale gp','gp.id = og.presale_id')
                        ->where('order_id',$order_id)->select()->toArray();

        array_walk($order_goods,function(&$val,$key){
            $val['subprice'] = round($val['subprice'],2);
            $val['o_status'] = code::get_ostatus_ch($val['o_status']);
        });
        $res['order_goods']['item'] = $order_goods;
        $res['order_goods']['subweight'] = round(array_sum(array_column($order_goods,'subweight')),2);
        $res['order_goods']['subprice'] = round(array_sum(array_column($order_goods,'subprice')),2);



        $order_action = $this->name('order_action')->where([
            ['order_id','=',$order_id],
            ['action_place','in',[0,2]]
        ])->order('log_time DESC,action_id DESC')->select();
        if($order_action){
            foreach ($order_action as &$item){
                $item['order_status']     = code::get_ostatus_ch($item['order_status']);
                $item['pay_status']       = code::ps[$item['pay_status']];
                $item['shipping_status']  = code::ss[$item['shipping_status']];
                $item['log_time']         = date('Y-m-d H:i:s',$item['log_time']);
            }
            $res['order_action'] = $order_action;
        }
        return $res;
    }
}