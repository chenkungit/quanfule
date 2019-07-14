<?php 
return array (
  'comment_id' => 
  array (
    'name' => 'comment_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'comment_type' => 
  array (
    'name' => 'comment_type',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'id_value' => 
  array (
    'name' => 'id_value',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'email' => 
  array (
    'name' => 'email',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'user_name' => 
  array (
    'name' => 'user_name',
    'type' => 'varchar(200)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'content' => 
  array (
    'name' => 'content',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'comment_rank' => 
  array (
    'name' => 'comment_rank',
    'type' => 'tinyint(1) unsigned',
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
  'ip_address' => 
  array (
    'name' => 'ip_address',
    'type' => 'varchar(15)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'status' => 
  array (
    'name' => 'status',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'parent_id' => 
  array (
    'name' => 'parent_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'to_fd' => 
  array (
    'name' => 'to_fd',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'imgs' => 
  array (
    'name' => 'imgs',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'comment_yx' => 
  array (
    'name' => 'comment_yx',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'rec_id' => 
  array (
    'name' => 'rec_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'order_id' => 
  array (
    'name' => 'order_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_attr' => 
  array (
    'name' => 'goods_attr',
    'type' => 'varchar(120)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_micro' => 
  array (
    'name' => 'is_micro',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_read' => 
  array (
    'name' => 'is_read',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_official' => 
  array (
    'name' => 'is_official',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_twice' => 
  array (
    'name' => 'is_twice',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);