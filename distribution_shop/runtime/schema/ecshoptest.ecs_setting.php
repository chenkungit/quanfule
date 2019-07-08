<?php 
return array (
  'setting_id' => 
  array (
    'name' => 'setting_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'setting_key' => 
  array (
    'name' => 'setting_key',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'setting_value' => 
  array (
    'name' => 'setting_value',
    'type' => 'varchar(50)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);