<?php
/**
 * User: cpj
 * Date: 2019/6/28
 */

namespace app\common\Mapping;


interface PaymentInterface
{
    public function awake($orderSn, $totalAmount,$arg);
}