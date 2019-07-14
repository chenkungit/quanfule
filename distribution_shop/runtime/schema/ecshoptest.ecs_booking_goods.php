<?php 
return array (
  'rec_id' => 
  array (
    'name' => 'rec_id',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
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
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'link_man' => 
  array (
    'name' => 'link_man',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'tel' => 
  array (
    'name' => 'tel',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_id' => 
  array (
    'name' => 'goods_id',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_desc' => 
  array (
    'name' => 'goods_desc',
    'type' => 'varchar(255)',
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
  'booking_time' => 
  array (
    'name' => 'booking_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_dispose' => 
  array (
    'name' => 'is_dispose',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'dispose_user' => 
  array (
    'name' => 'dispose_user',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'dispose_time' => 
  array (
    'name' => 'dispose_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'dispose_note' => 
  array (
    'name' => 'dispose_note',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
);