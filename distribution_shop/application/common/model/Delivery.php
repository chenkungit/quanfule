<?php
namespace app\common\model;

use think\Model;

class Delivery extends Model
{

    public function delivery_tracking($order_id){

        $res = $this->name('delivery_order')->where('order_id',$order_id)->find();

        $res['invoice_no'];
        $res['shipping_id'];

    }


    private function switch_shipping($shipping_id){
        switch ($shipping_id){
            case "6":
                $postcom = 'yuantong';//圆通快递
                break;
            case "22":
                $postcom = 'shentong';  //申通
                break;
            case "24":
                $postcom = 'yuantong';      //苏州圆通
                break;
        }
        return $postcom;
    }
}