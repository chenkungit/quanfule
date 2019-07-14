<?php 
return array (
  'sa_id' => 
  array (
    'name' => 'sa_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'map_store_id' => 
  array (
    'name' => 'map_store_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'activity_name' => 
  array (
    'name' => 'activity_name',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'icon' => 
  array (
    'name' => 'icon',
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
  'beg_time' => 
  array (
    'name' => 'beg_time',
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
  'sort' => 
  array (
    'name' => 'sort',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'enabled' => 
  array (
    'name' => 'enabled',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);