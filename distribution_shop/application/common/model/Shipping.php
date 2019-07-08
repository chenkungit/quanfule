<?php
namespace app\common\model;

use think\Config;
use think\Model;
use express;
use zxwl\top\domain\AddressInfo;
use zxwl\top\domain\IntelliLogisticsParam;
use zxwl\top\domain\OrderInfo;
use zxwl\top\domain\TradeOrder;
use zxwl\top\request\CainiaoLogisticsIntelliIGetRequest;
use zxwl\top\TopClient;

class Shipping extends Model
{
    /**
     * 取得可用的配送方式列表
     * @param   string   $region_id_list     收货人地区id数组（包括国家、省、市、区）
     * @param   int   $sendaddress     发货地id
     * @param  array $supplier_ids 所有商品供应商
     * @return  array   配送方式数组
     */
    public function available_shipping_list($region_id_list,$sendaddress,$supplier_ids)
    {
        //

        $fields = 's.sendaddress,s.shipping_desc, s.insure, s.support_cod, a.configure,s.shipping_id, s.shipping_code, s.shipping_name';

        $where = [
            ['r.region_id','in',"1,$region_id_list"],
            ['sp.sendaddress_id','=',$sendaddress],
            ['enable','=',1]
        ];

//        if($zxwl_code){
//            $sql .= " AND s.shipping_id =$zxwl_code ";
//        }
        $shipping_list = $this->name('shipping')->alias('s')->field($fields)->where($where)
            ->leftJoin('ecs_sendaddress_shipping sp','sp.shipping_id = s.shipping_id')
            ->leftJoin('ecs_shipping_area a','a.shipping_id = s.shipping_id')
            ->leftJoin('ecs_area_region r','r.shipping_area_id = a.shipping_area_id')
            ->select()->toArray();

        $wuliu = true;
        /*-全部植物的单子不显示物流-*/
        foreach ($supplier_ids as $val){
            if($val !== 26){
                if($val !== 46){
                    $wuliu = false;
                    break;
                }
            }
        }

        foreach ($shipping_list AS $key => &$val){

            $val['configure'] = unserialize_config($val['configure']);
            if($val['shipping_id'] == 29){
                unset($shipping_list[$key]);
                continue;
            }
            if($val['shipping_id'] == 33 && $wuliu == false){
                unset($shipping_list[$key]);
                continue;
            }
        }

        return $shipping_list;

    }


    /**
     * 选中快递方式邮费计算
     * @param   String   $region_id_list     收货人地区id数组（包括国家、省、市、区）
     * @param   int   $shipping_code     快递方式id
     * @return  array   选中快递方式的configure
     */
    public function chosen_shipping_fee($region_id_list,$sendaddress,$shipping_code,$goods_weight, $goods_amount, $goods_number=''){

        $fields = 's.sendaddress,s.shipping_desc, s.insure, s.support_cod, a.configure,s.shipping_id, s.shipping_code, s.shipping_name';

        $where = [
            ['r.region_id','in',"1,$region_id_list"],
            ['s.shipping_id','=',$shipping_code]
        ];

        $res = $this->name('shipping')->alias('s')->field($fields)->where($where)
            ->leftJoin('ecs_sendaddress_shipping sp','sp.shipping_id = s.shipping_id')
            ->leftJoin('ecs_shipping_area a','a.shipping_id = s.shipping_id')
            ->leftJoin('ecs_area_region r','r.shipping_area_id = a.shipping_area_id')
            ->group('s.shipping_id')->find();

         if($res){
             $res['configure'] = unserialize_config($res['configure']);

             return $this->shipping_fee($shipping_code,$res['configure'],$goods_weight, $goods_amount, $goods_number);
         }else{
             abort(500, '地址错误');
         }

    }

    public function shipping_fee($shipping_code, $shipping_config, $goods_weight, $goods_amount, $goods_number='')
    {
        if($goods_weight<=0){
            return 0;
        }
        /*-员工自提-*/
        if($shipping_code== 29){
            return 0;
        }

        $obj  = new express($shipping_config);
        $fee = $obj->calculate($goods_weight, $goods_amount, $goods_number);

        $low_price = $this->name('shipping')->where('shipping_code',$shipping_code)->value('low_price');
        if($shipping_code == 41 || $shipping_code == 33){
            if($fee < 60){
                $fee = 60;
            }
        }

        if($fee<floatval($low_price))
        {
            return $low_price;
        }
        else
        {
            return $fee;
        }
    }
    /**
     * 取得某配送方式对应于某收货地址的区域信息
     * @param   int     $shipping_id        配送方式id
     * @param   array   $region_id_list     收货人地区id数组
     * @return  array   配送区域信息（config 对应着反序列化的 configure）
     */
    public function shipping_area_info($shipping_id, $region_id_list)
    {

        $sql = 'SELECT s.shipping_code, s.shipping_name, ' .
            's.shipping_desc, s.insure, s.support_cod, a.configure ' .
            'FROM ecs_shipping AS s, ' .
            'ecs_shipping_area AS a, ' .
            'ecs_area_region AS r ' .
            "WHERE s.shipping_id = '$shipping_id' " .
            'AND r.region_id in(' .$region_id_list.')' .
            ' AND r.shipping_area_id = a.shipping_area_id AND a.shipping_id = s.shipping_id AND s.enabled = 1';

        $row = $this->query($sql)[0];

        if (!empty($row))
        {

            $shipping_config = unserialize_config($row['configure']);
            if (isset($shipping_config['pay_fee']))
            {
                if (strpos($shipping_config['pay_fee'], '%') !== false)
                {
                    $row['pay_fee'] = floatval($shipping_config['pay_fee']) . '%';
                }
                else
                {
                    $row['pay_fee'] = floatval($shipping_config['pay_fee']);
                }
            }
            else
            {
                $row['pay_fee'] = '0.00';
            }
        }

        return $row;
    }
}