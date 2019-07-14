<?php 
return array (
  'act_id' => 
  array (
    'name' => 'act_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'act_name' => 
  array (
    'name' => 'act_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'shops_id' => 
  array (
    'name' => 'shops_id',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'start_time' => 
  array (
    'name' => 'start_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'end_time' => 
  array (
    'name' => 'end_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'user_rank' => 
  array (
    'name' => 'user_rank',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'act_range' => 
  array (
    'name' => 'act_range',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'act_range_ext' => 
  array (
    'name' => 'act_range_ext',
    'type' => 'varchar(25500)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'min_amount' => 
  array (
    'name' => 'min_amount',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'max_amount' => 
  array (
    'name' => 'max_amount',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'qty' => 
  array (
    'name' => 'qty',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'act_type' => 
  array (
    'name' => 'act_type',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'act_type_ext' => 
  array (
    'name' => 'act_type_ext',
    'type' => 'decimal(10,2) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_repeat' => 
  array (
    'name' => 'is_repeat',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'gift' => 
  array (
    'name' => 'gift',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'sort_order' => 
  array (
    'name' => 'sort_order',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '50',
    'primary' => false,
    'autoinc' => false,
  ),
  'use_scope' => 
  array (
    'name' => 'use_scope',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'show_in_cart' => 
  array (
    'name' => 'show_in_cart',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'in_cart_str' => 
  array (
    'name' => 'in_cart_str',
    'type' => 'varchar(320)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);