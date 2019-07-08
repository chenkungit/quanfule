<?php 
return array (
  's_id' => 
  array (
    'name' => 's_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  's_name' => 
  array (
    'name' => 's_name',
    'type' => 'varchar(50)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  's_level' => 
  array (
    'name' => 's_level',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  's_type' => 
  array (
    'name' => 's_type',
    'type' => 'tinyint(4)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'sort' => 
  array (
    'name' => 'sort',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'enabled' => 
  array (
    'name' => 'enabled',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
);