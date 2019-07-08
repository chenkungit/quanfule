<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'mediumint(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'mobile' => 
  array (
    'name' => 'mobile',
    'type' => 'varchar(100)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'template_id' => 
  array (
    'name' => 'template_id',
    'type' => 'mediumint(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'sms_content' => 
  array (
    'name' => 'sms_content',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'error' => 
  array (
    'name' => 'error',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'pri' => 
  array (
    'name' => 'pri',
    'type' => 'tinyint(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'last_send' => 
  array (
    'name' => 'last_send',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);