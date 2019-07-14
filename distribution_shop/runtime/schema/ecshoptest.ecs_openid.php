<?php 
return array (
  'o_id' => 
  array (
    'name' => 'o_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'platform' => 
  array (
    'name' => 'platform',
    'type' => 'enum(\'app\',\'mini_program\',\'official_accounts\')',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'openid' => 
  array (
    'name' => 'openid',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);