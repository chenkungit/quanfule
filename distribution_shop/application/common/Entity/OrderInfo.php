<?php

namespace app\common\Entity;


use app\common\model\BaseModel;


/**
 * @property int $order_id
 * @property int $user_id
 * @property float $surplus
 * @property string $order_sn
 * @property string $pay_time
 * @property int $is_deal
 * @property float $goods_amount
 * @property float $vip_goods_amount
 * @property float $shipping_fee
 */
class OrderInfo extends BaseModel
{
    protected $table = 'ecs_order_info';


    /* @return OrderInfo */
    public static function getInfoByOrderId(int $orderId, $fields = '*')
    {
        return static::field($fields)->where('order_id', $orderId)->find();
    }
}