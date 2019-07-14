<?php 
return array (
  'shipping_id' => 
  array (
    'name' => 'shipping_id',
    'type' => 'int(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'shipping_name' => 
  array (
    'name' => 'shipping_name',
    'type' => 'varchar(120)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'enable' => 
  array (
    'name' => 'enable',
    'type' => 'tinyint(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'sort' => 
  array (
    'name' => 'sort',
    'type' => 'int(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);