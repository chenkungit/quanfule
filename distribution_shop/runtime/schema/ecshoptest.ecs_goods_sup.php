<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'marketing_type' => 
  array (
    'name' => 'marketing_type',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'vs_id' => 
  array (
    'name' => 'vs_id',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_dingjin' => 
  array (
    'name' => 'is_dingjin',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_yuding' => 
  array (
    'name' => 'is_yuding',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_crowdfunding' => 
  array (
    'name' => 'is_crowdfunding',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'comment_yx' => 
  array (
    'name' => 'comment_yx',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'name' => 
  array (
    'name' => 'name',
    'type' => 'varchar(100)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'shortname' => 
  array (
    'name' => 'shortname',
    'type' => 'varchar(40)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'tbsm' => 
  array (
    'name' => 'tbsm',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'K3_name' => 
  array (
    'name' => 'K3_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'code' => 
  array (
    'name' => 'code',
    'type' => 'varchar(50)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'stock' => 
  array (
    'name' => 'stock',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'category' => 
  array (
    'name' => 'category',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'brand' => 
  array (
    'name' => 'brand',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'supplier' => 
  array (
    'name' => 'supplier',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'price' => 
  array (
    'name' => 'price',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'app_rate' => 
  array (
    'name' => 'app_rate',
    'type' => 'float(4,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'price_temp' => 
  array (
    'name' => 'price_temp',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'img' => 
  array (
    'name' => 'img',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'thumbnail' => 
  array (
    'name' => 'thumbnail',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'album' => 
  array (
    'name' => 'album',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'descpt' => 
  array (
    'name' => 'descpt',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'm_descpt' => 
  array (
    'name' => 'm_descpt',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'weight' => 
  array (
    'name' => 'weight',
    'type' => 'decimal(10,3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'use_points' => 
  array (
    'name' => 'use_points',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'rank_points' => 
  array (
    'name' => 'rank_points',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'type' => 
  array (
    'name' => 'type',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '7',
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
  'is_virtual' => 
  array (
    'name' => 'is_virtual',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_sale' => 
  array (
    'name' => 'is_sale',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'edit_time' => 
  array (
    'name' => 'edit_time',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => NULL,
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
  'package' => 
  array (
    'name' => 'package',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'icon' => 
  array (
    'name' => 'icon',
    'type' => 'int(4)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'ord' => 
  array (
    'name' => 'ord',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'isbind' => 
  array (
    'name' => 'isbind',
    'type' => 'tinyint(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'bind_ids' => 
  array (
    'name' => 'bind_ids',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_you' => 
  array (
    'name' => 'is_you',
    'type' => 'tinyint(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_multiprice' => 
  array (
    'name' => 'is_multiprice',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_mprice' => 
  array (
    'name' => 'is_mprice',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'start_num' => 
  array (
    'name' => 'start_num',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'Nbei' => 
  array (
    'name' => 'Nbei',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_miao' => 
  array (
    'name' => 'is_miao',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'rel_articles_id' => 
  array (
    'name' => 'rel_articles_id',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'rel_goods_id' => 
  array (
    'name' => 'rel_goods_id',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_nei' => 
  array (
    'name' => 'is_nei',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'youhui_type' => 
  array (
    'name' => 'youhui_type',
    'type' => 'varchar(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'youhui_desc' => 
  array (
    'name' => 'youhui_desc',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_desc' => 
  array (
    'name' => 'shipping_desc',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'tishi_desc' => 
  array (
    'name' => 'tishi_desc',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'video_url' => 
  array (
    'name' => 'video_url',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'video_img' => 
  array (
    'name' => 'video_img',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'asked_question' => 
  array (
    'name' => 'asked_question',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_limit' => 
  array (
    'name' => 'is_limit',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_fei5zhe' => 
  array (
    'name' => 'is_fei5zhe',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_k3name' => 
  array (
    'name' => 'is_k3name',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'push_time' => 
  array (
    'name' => 'push_time',
    'type' => 'timestamp',
    'notnull' => false,
    'default' => '1970-02-01 08:00:00',
    'primary' => false,
    'autoinc' => false,
  ),
);