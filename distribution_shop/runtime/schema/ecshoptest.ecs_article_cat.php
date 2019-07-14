<?php 
return array (
  'cat_id' => 
  array (
    'name' => 'cat_id',
    'type' => 'smallint(5)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'picurl' => 
  array (
    'name' => 'picurl',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'cat_name' => 
  array (
    'name' => 'cat_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'cat_type' => 
  array (
    'name' => 'cat_type',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'keywords' => 
  array (
    'name' => 'keywords',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'cat_desc' => 
  array (
    'name' => 'cat_desc',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
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
  'show_in_nav' => 
  array (
    'name' => 'show_in_nav',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
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
  'show_type' => 
  array (
    'name' => 'show_type',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);