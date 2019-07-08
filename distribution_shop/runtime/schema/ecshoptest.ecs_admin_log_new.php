<?php 
return array (
  'log_id' => 
  array (
    'name' => 'log_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'log_time' => 
  array (
    'name' => 'log_time',
    'type' => 'timestamp',
    'notnull' => false,
    'default' => '1970-02-01 08:00:00',
    'primary' => false,
    'autoinc' => false,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'function' => 
  array (
    'name' => 'function',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'method' => 
  array (
    'name' => 'method',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'operate_id' => 
  array (
    'name' => 'operate_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
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
);