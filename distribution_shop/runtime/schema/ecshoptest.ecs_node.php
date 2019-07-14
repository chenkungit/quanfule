<?php 
return array (
  'node_id' => 
  array (
    'name' => 'node_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'parent_id' => 
  array (
    'name' => 'parent_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'level' => 
  array (
    'name' => 'level',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'router_code' => 
  array (
    'name' => 'router_code',
    'type' => 'char(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'node_name' => 
  array (
    'name' => 'node_name',
    'type' => 'varchar(50)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'order' => 
  array (
    'name' => 'order',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);