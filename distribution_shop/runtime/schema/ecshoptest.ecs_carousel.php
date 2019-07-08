<?php 
return array (
  'carousel_id' => 
  array (
    'name' => 'carousel_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'carousel_name' => 
  array (
    'name' => 'carousel_name',
    'type' => 'varchar(50)',
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
  'redirect_type' => 
  array (
    'name' => 'redirect_type',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'redirect_id' => 
  array (
    'name' => 'redirect_id',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'redirect_name' => 
  array (
    'name' => 'redirect_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'carousel_type' => 
  array (
    'name' => 'carousel_type',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'carousel_position' => 
  array (
    'name' => 'carousel_position',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
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
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'timestamp',
    'notnull' => false,
    'default' => '1970-02-01 08:00:00',
    'primary' => false,
    'autoinc' => false,
  ),
  'end_time' => 
  array (
    'name' => 'end_time',
    'type' => 'timestamp',
    'notnull' => false,
    'default' => '1970-02-01 08:00:00',
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