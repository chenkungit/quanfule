<?php 
return array (
  'pa_id' => 
  array (
    'name' => 'pa_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'activity_name' => 
  array (
    'name' => 'activity_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'range' => 
  array (
    'name' => 'range',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'user_rank' => 
  array (
    'name' => 'user_rank',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'type' => 
  array (
    'name' => 'type',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'number' => 
  array (
    'name' => 'number',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'amount' => 
  array (
    'name' => 'amount',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'present_sup_id' => 
  array (
    'name' => 'present_sup_id',
    'type' => 'int(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'present_shp_id' => 
  array (
    'name' => 'present_shp_id',
    'type' => 'int(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'present_sup_name' => 
  array (
    'name' => 'present_sup_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_repeat' => 
  array (
    'name' => 'is_repeat',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
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
  'album' => 
  array (
    'name' => 'album',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);