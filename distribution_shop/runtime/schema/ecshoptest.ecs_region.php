<?php 
return array (
  'region_id' => 
  array (
    'name' => 'region_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
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
  'region_name' => 
  array (
    'name' => 'region_name',
    'type' => 'varchar(120)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'region_type' => 
  array (
    'name' => 'region_type',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '2',
    'primary' => false,
    'autoinc' => false,
  ),
  'agency_id' => 
  array (
    'name' => 'agency_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);