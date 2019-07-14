<?php 
return array (
  'img_id' => 
  array (
    'name' => 'img_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'cat_id' => 
  array (
    'name' => 'cat_id',
    'type' => 'smallint(5)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'img_title' => 
  array (
    'name' => 'img_title',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'img_desc' => 
  array (
    'name' => 'img_desc',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'img_url' => 
  array (
    'name' => 'img_url',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'href_url' => 
  array (
    'name' => 'href_url',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
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
);