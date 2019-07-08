<?php 
return array (
  'goods_id' => 
  array (
    'name' => 'goods_id',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => true,
    'autoinc' => false,
  ),
  'link_goods_id' => 
  array (
    'name' => 'link_goods_id',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => true,
    'autoinc' => false,
  ),
  'is_double' => 
  array (
    'name' => 'is_double',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'admin_id' => 
  array (
    'name' => 'admin_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => true,
    'autoinc' => false,
  ),
);