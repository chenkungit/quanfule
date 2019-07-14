<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => true,
  ),
  'order_id' => 
  array (
    'name' => 'order_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'delivery_id' => 
  array (
    'name' => 'delivery_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'delivery_sn' => 
  array (
    'name' => 'delivery_sn',
    'type' => 'varchar(32)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'package_id' => 
  array (
    'name' => 'package_id',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'package_center_code' => 
  array (
    'name' => 'package_center_code',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'package_center_name' => 
  array (
    'name' => 'package_center_name',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'print_config' => 
  array (
    'name' => 'print_config',
    'type' => 'varchar(2550)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_branch_code' => 
  array (
    'name' => 'shipping_branch_code',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_branch_name' => 
  array (
    'name' => 'shipping_branch_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'short_address' => 
  array (
    'name' => 'short_address',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'waybill_code' => 
  array (
    'name' => 'waybill_code',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'cp_code' => 
  array (
    'name' => 'cp_code',
    'type' => 'varchar(16)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);