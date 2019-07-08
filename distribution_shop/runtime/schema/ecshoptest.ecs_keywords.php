<?php 
return array (
  'date' => 
  array (
    'name' => 'date',
    'type' => 'date',
    'notnull' => false,
    'default' => '1970-02-01',
    'primary' => true,
    'autoinc' => false,
  ),
  'searchengine' => 
  array (
    'name' => 'searchengine',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => '',
    'primary' => true,
    'autoinc' => false,
  ),
  'keyword' => 
  array (
    'name' => 'keyword',
    'type' => 'varchar(90)',
    'notnull' => false,
    'default' => '',
    'primary' => true,
    'autoinc' => false,
  ),
  'count' => 
  array (
    'name' => 'count',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);