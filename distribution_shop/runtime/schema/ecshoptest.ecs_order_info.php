<?php 
return array (
  'order_id' => 
  array (
    'name' => 'order_id',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'order_sn' => 
  array (
    'name' => 'order_sn',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'order_no' => 
  array (
    'name' => 'order_no',
    'type' => 'varchar(32)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'sendaddress' => 
  array (
    'name' => 'sendaddress',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'consignee' => 
  array (
    'name' => 'consignee',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'country' => 
  array (
    'name' => 'country',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'province' => 
  array (
    'name' => 'province',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'city' => 
  array (
    'name' => 'city',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'district' => 
  array (
    'name' => 'district',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'address' => 
  array (
    'name' => 'address',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'phone' => 
  array (
    'name' => 'phone',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_id' => 
  array (
    'name' => 'shipping_id',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_name' => 
  array (
    'name' => 'shipping_name',
    'type' => 'varchar(120)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_fee' => 
  array (
    'name' => 'shipping_fee',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'pay_id' => 
  array (
    'name' => 'pay_id',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'pay_name' => 
  array (
    'name' => 'pay_name',
    'type' => 'varchar(120)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'bonus_id' => 
  array (
    'name' => 'bonus_id',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'bonus' => 
  array (
    'name' => 'bonus',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'bonus_ship_id' => 
  array (
    'name' => 'bonus_ship_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'bonus_ship' => 
  array (
    'name' => 'bonus_ship',
    'type' => 'float(8,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'surplus' => 
  array (
    'name' => 'surplus',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'discount' => 
  array (
    'name' => 'discount',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'discount_card' => 
  array (
    'name' => 'discount_card',
    'type' => 'float(8,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_amount' => 
  array (
    'name' => 'goods_amount',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'money_paid' => 
  array (
    'name' => 'money_paid',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'order_amount' => 
  array (
    'name' => 'order_amount',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'vip_goods_amount' => 
  array (
    'name' => 'vip_goods_amount',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'referer' => 
  array (
    'name' => 'referer',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'o_status' => 
  array (
    'name' => 'o_status',
    'type' => 'tinyint(2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'p_status' => 
  array (
    'name' => 'p_status',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'd_status' => 
  array (
    'name' => 'd_status',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'r_status' => 
  array (
    'name' => 'r_status',
    'type' => 'tinyint(2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'pay_time' => 
  array (
    'name' => 'pay_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_time' => 
  array (
    'name' => 'shipping_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'refund_time' => 
  array (
    'name' => 'refund_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'finish_time' => 
  array (
    'name' => 'finish_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'refund_reason' => 
  array (
    'name' => 'refund_reason',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'msg_to_buyer' => 
  array (
    'name' => 'msg_to_buyer',
    'type' => 'varchar(2500)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'msg_to_shop' => 
  array (
    'name' => 'msg_to_shop',
    'type' => 'varchar(2500)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'invoice_no' => 
  array (
    'name' => 'invoice_no',
    'type' => 'varchar(2550)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'print_info' => 
  array (
    'name' => 'print_info',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'print_info_shops' => 
  array (
    'name' => 'print_info_shops',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'from_mobile' => 
  array (
    'name' => 'from_mobile',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_deal' => 
  array (
    'name' => 'is_deal',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_delete' => 
  array (
    'name' => 'is_delete',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_comment' => 
  array (
    'name' => 'is_comment',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'label' => 
  array (
    'name' => 'label',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'from_where' => 
  array (
    'name' => 'from_where',
    'type' => 'varchar(32)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'created_time' => 
  array (
    'name' => 'created_time',
    'type' => 'datetime',
    'notnull' => false,
    'default' => 'CURRENT_TIMESTAMP',
    'primary' => false,
    'autoinc' => false,
  ),
  'updated_time' => 
  array (
    'name' => 'updated_time',
    'type' => 'datetime',
    'notnull' => false,
    'default' => 'CURRENT_TIMESTAMP',
    'primary' => false,
    'autoinc' => false,
  ),
);