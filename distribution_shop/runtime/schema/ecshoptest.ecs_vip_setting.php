<?php 
return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'name' => 
  array (
    'name' => 'name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'level' => 
  array (
    'name' => 'level',
    'type' => 'int(11)',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'discount' => 
  array (
    'name' => 'discount',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'achievement_award' => 
  array (
    'name' => 'achievement_award',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'first_reback_rate' => 
  array (
    'name' => 'first_reback_rate',
    'type' => 'decimal(10,3)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'second_reback_rate' => 
  array (
    'name' => 'second_reback_rate',
    'type' => 'decimal(10,3)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'third_reback_rate' => 
  array (
    'name' => 'third_reback_rate',
    'type' => 'decimal(10,3)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'capping_rate' => 
  array (
    'name' => 'capping_rate',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'capping_price' => 
  array (
    'name' => 'capping_price',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'recommend_award' => 
  array (
    'name' => 'recommend_award',
    'type' => 'decimal(10,2)',
    'notnull' => false,
    'default' => '0.00',
    'primary' => false,
    'autoinc' => false,
  ),
  'created_time' => 
  array (
    'name' => 'created_time',
    'type' => 'datetime',
    'notnull' => false,
    'default' => 'CURRENT_TIMESTAMP',
    'primary' => false,
    'autoinc' => false,
  ),
  'updated_time' => 
  array (
    'name' => 'updated_time',
    'type' => 'datetime',
    'notnull' => false,
    'default' => 'CURRENT_TIMESTAMP',
    'primary' => false,
    'autoinc' => false,
  ),
);