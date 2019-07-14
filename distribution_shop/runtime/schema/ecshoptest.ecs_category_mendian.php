<?php 
return array (
  'cat_id' => 
  array (
    'name' => 'cat_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'cat_name' => 
  array (
    'name' => 'cat_name',
    'type' => 'varchar(90)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'mobile_icon' => 
  array (
    'name' => 'mobile_icon',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'parent_id' => 
  array (
    'name' => 'parent_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'sort_order' => 
  array (
    'name' => 'sort_order',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '50',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_show' => 
  array (
    'name' => 'is_show',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
);