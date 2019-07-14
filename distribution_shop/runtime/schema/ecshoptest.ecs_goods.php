<?php 
return array (
  'goods_id' => 
  array (
    'name' => 'goods_id',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'cat_id' => 
  array (
    'name' => 'cat_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_sn' => 
  array (
    'name' => 'goods_sn',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_name' => 
  array (
    'name' => 'goods_name',
    'type' => 'varchar(120)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'wx_goods_name' => 
  array (
    'name' => 'wx_goods_name',
    'type' => 'varchar(120)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_name_style' => 
  array (
    'name' => 'goods_name_style',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '+',
    'primary' => false,
    'autoinc' => false,
  ),
  'click_count' => 
  array (
    'name' => 'click_count',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'brand_id' => 
  array (
    'name' => 'brand_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'provider_name' => 
  array (
    'name' => 'provider_name',
    'type' => 'varchar(100)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_number' => 
  array (
    'name' => 'goods_number',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_weight' => 
  array (
    'name' => 'goods_weight',
    'type' => 'decimal(10,3) unsigned',
    'notnull' => false,
    'default' => '0.000',
    'primary' => false,
    'autoinc' => false,
  ),
  'market_price' => 
  array (
    'name' => 'market_price',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'shop_price' => 
  array (
    'name' => 'shop_price',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'wx_price' => 
  array (
    'name' => 'wx_price',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'promote_price' => 
  array (
    'name' => 'promote_price',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'promote_start_date' => 
  array (
    'name' => 'promote_start_date',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'promote_end_date' => 
  array (
    'name' => 'promote_end_date',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'warn_number' => 
  array (
    'name' => 'warn_number',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'keywords' => 
  array (
    'name' => 'keywords',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_brief' => 
  array (
    'name' => 'goods_brief',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_desc' => 
  array (
    'name' => 'goods_desc',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'wx_goods_desc' => 
  array (
    'name' => 'wx_goods_desc',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_thumb' => 
  array (
    'name' => 'goods_thumb',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_img' => 
  array (
    'name' => 'goods_img',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'original_img' => 
  array (
    'name' => 'original_img',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_real' => 
  array (
    'name' => 'is_real',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'extension_code' => 
  array (
    'name' => 'extension_code',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_on_sale' => 
  array (
    'name' => 'is_on_sale',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_alone_sale' => 
  array (
    'name' => 'is_alone_sale',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_shipping' => 
  array (
    'name' => 'is_shipping',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'integral' => 
  array (
    'name' => 'integral',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
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
  'sort_order' => 
  array (
    'name' => 'sort_order',
    'type' => 'smallint(4) unsigned',
    'notnull' => false,
    'default' => '100',
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
  'is_best' => 
  array (
    'name' => 'is_best',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_new' => 
  array (
    'name' => 'is_new',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_hot' => 
  array (
    'name' => 'is_hot',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_promote' => 
  array (
    'name' => 'is_promote',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'bonus_type_id' => 
  array (
    'name' => 'bonus_type_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'last_update' => 
  array (
    'name' => 'last_update',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_type' => 
  array (
    'name' => 'goods_type',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'seller_note' => 
  array (
    'name' => 'seller_note',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'give_integral' => 
  array (
    'name' => 'give_integral',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '-1',
    'primary' => false,
    'autoinc' => false,
  ),
  'rank_integral' => 
  array (
    'name' => 'rank_integral',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '-1',
    'primary' => false,
    'autoinc' => false,
  ),
  'suppliers_id' => 
  array (
    'name' => 'suppliers_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_on_wxsale' => 
  array (
    'name' => 'is_on_wxsale',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_check' => 
  array (
    'name' => 'is_check',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_onshow' => 
  array (
    'name' => 'is_onshow',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'wx_fare' => 
  array (
    'name' => 'wx_fare',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'check_status' => 
  array (
    'name' => 'check_status',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'check_cause' => 
  array (
    'name' => 'check_cause',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'check_user' => 
  array (
    'name' => 'check_user',
    'type' => 'int(12)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_display' => 
  array (
    'name' => 'is_display',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'sales_count' => 
  array (
    'name' => 'sales_count',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);