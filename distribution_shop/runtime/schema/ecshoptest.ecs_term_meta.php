<?php 
return array (
  'meta_id' => 
  array (
    'name' => 'meta_id',
    'type' => 'bigint(20) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'object_type' => 
  array (
    'name' => 'object_type',
    'type' => 'char(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'object_group' => 
  array (
    'name' => 'object_group',
    'type' => 'char(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'object_id' => 
  array (
    'name' => 'object_id',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'meta_key' => 
  array (
    'name' => 'meta_key',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'meta_value' => 
  array (
    'name' => 'meta_value',
    'type' => 'longtext',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);