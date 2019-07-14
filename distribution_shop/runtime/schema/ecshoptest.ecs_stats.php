<?php 
return array (
  'access_time' => 
  array (
    'name' => 'access_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'ip_address' => 
  array (
    'name' => 'ip_address',
    'type' => 'varchar(15)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'visit_times' => 
  array (
    'name' => 'visit_times',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'browser' => 
  array (
    'name' => 'browser',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'system' => 
  array (
    'name' => 'system',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'language' => 
  array (
    'name' => 'language',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'area' => 
  array (
    'name' => 'area',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'referer_domain' => 
  array (
    'name' => 'referer_domain',
    'type' => 'varchar(100)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'referer_path' => 
  array (
    'name' => 'referer_path',
    'type' => 'varchar(200)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'access_url' => 
  array (
    'name' => 'access_url',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
);