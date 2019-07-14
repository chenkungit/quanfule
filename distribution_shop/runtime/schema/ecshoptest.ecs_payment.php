<?php 
return array (
  'pay_id' => 
  array (
    'name' => 'pay_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'pay_code' => 
  array (
    'name' => 'pay_code',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => '',
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
  'pay_fee' => 
  array (
    'name' => 'pay_fee',
    'type' => 'varchar(10)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'pay_desc' => 
  array (
    'name' => 'pay_desc',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'pay_order' => 
  array (
    'name' => 'pay_order',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'pay_config' => 
  array (
    'name' => 'pay_config',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'enabled' => 
  array (
    'name' => 'enabled',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_cod' => 
  array (
    'name' => 'is_cod',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_online' => 
  array (
    'name' => 'is_online',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);