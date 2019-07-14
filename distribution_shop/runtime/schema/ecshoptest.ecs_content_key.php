<?php 
return array (
  'key_id' => 
  array (
    'name' => 'key_id',
    'type' => 'smallint(5)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'key_name' => 
  array (
    'name' => 'key_name',
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
  'replace_num' => 
  array (
    'name' => 'replace_num',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'key_url' => 
  array (
    'name' => 'key_url',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);