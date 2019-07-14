<?php 
return array (
  'ad_id' => 
  array (
    'name' => 'ad_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'position_id' => 
  array (
    'name' => 'position_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'media_type' => 
  array (
    'name' => 'media_type',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'ad_name' => 
  array (
    'name' => 'ad_name',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'ad_link' => 
  array (
    'name' => 'ad_link',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'ad_code' => 
  array (
    'name' => 'ad_code',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'start_time' => 
  array (
    'name' => 'start_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'end_time' => 
  array (
    'name' => 'end_time',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'link_man' => 
  array (
    'name' => 'link_man',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'link_email' => 
  array (
    'name' => 'link_email',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'link_phone' => 
  array (
    'name' => 'link_phone',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'click_count' => 
  array (
    'name' => 'click_count',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'enabled' => 
  array (
    'name' => 'enabled',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
);