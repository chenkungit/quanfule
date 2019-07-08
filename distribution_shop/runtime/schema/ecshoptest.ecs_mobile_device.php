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
  'device_udid' => 
  array (
    'name' => 'device_udid',
    'type' => 'char(40)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'device_client' => 
  array (
    'name' => 'device_client',
    'type' => 'char(10)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'device_code' => 
  array (
    'name' => 'device_code',
    'type' => 'char(4)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'device_name' => 
  array (
    'name' => 'device_name',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'device_alias' => 
  array (
    'name' => 'device_alias',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'device_token' => 
  array (
    'name' => 'device_token',
    'type' => 'char(64)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'device_os' => 
  array (
    'name' => 'device_os',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'int(9)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_admin' => 
  array (
    'name' => 'is_admin',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'location_province' => 
  array (
    'name' => 'location_province',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'location_city' => 
  array (
    'name' => 'location_city',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'in_status' => 
  array (
    'name' => 'in_status',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);