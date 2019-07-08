<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'start_time' => 
  array (
    'name' => 'start_time',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'end_time' => 
  array (
    'name' => 'end_time',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'money_low' => 
  array (
    'name' => 'money_low',
    'type' => 'float(8,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'money_start' => 
  array (
    'name' => 'money_start',
    'type' => 'float(8,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'money_end' => 
  array (
    'name' => 'money_end',
    'type' => 'float(8,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'enable' => 
  array (
    'name' => 'enable',
    'type' => 'int(2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);