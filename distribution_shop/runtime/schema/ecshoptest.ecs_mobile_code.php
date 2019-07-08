<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'mobile' => 
  array (
    'name' => 'mobile',
    'type' => 'varchar(12)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'code' => 
  array (
    'name' => 'code',
    'type' => 'varchar(4)',
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