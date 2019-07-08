<?php 
return array (
  'shipping_area_id' => 
  array (
    'name' => 'shipping_area_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'shipping_area_name' => 
  array (
    'name' => 'shipping_area_name',
    'type' => 'varchar(150)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'shipping_id' => 
  array (
    'name' => 'shipping_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'configure' => 
  array (
    'name' => 'configure',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);