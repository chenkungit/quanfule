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
  'exchange_integral' => 
  array (
    'name' => 'exchange_integral',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_exchange' => 
  array (
    'name' => 'is_exchange',
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
);