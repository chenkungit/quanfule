<?php 
return array (
  'position_id' => 
  array (
    'name' => 'position_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'position_name' => 
  array (
    'name' => 'position_name',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'ad_width' => 
  array (
    'name' => 'ad_width',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'ad_height' => 
  array (
    'name' => 'ad_height',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'position_desc' => 
  array (
    'name' => 'position_desc',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'position_style' => 
  array (
    'name' => 'position_style',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);