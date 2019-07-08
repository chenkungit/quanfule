<?php 
return array (
  'from_ad' => 
  array (
    'name' => 'from_ad',
    'type' => 'smallint(5)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'referer' => 
  array (
    'name' => 'referer',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'clicks' => 
  array (
    'name' => 'clicks',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
);