<?php 
return array (
  'ap_id' => 
  array (
    'name' => 'ap_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'body' => 
  array (
    'name' => 'body',
    'type' => 'longtext',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'label' => 
  array (
    'name' => 'label',
    'type' => 'longtext',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'page_type' => 
  array (
    'name' => 'page_type',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'bonus_ids' => 
  array (
    'name' => 'bonus_ids',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);