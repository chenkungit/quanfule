<?php 
return array (
  'item_id' => 
  array (
    'name' => 'item_id',
    'type' => 'mediumint(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => false,
  ),
  'type' => 
  array (
    'name' => 'type',
    'type' => 'varchar(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => false,
  ),
  'starttime' => 
  array (
    'name' => 'starttime',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'endtime' => 
  array (
    'name' => 'endtime',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);