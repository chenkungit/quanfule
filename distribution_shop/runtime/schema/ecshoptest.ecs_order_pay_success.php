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
  'out_trade_no' => 
  array (
    'name' => 'out_trade_no',
    'type' => 'varchar(32)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'trade_no' => 
  array (
    'name' => 'trade_no',
    'type' => 'varchar(32)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'total_fee' => 
  array (
    'name' => 'total_fee',
    'type' => 'float(8,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'refund_fee' => 
  array (
    'name' => 'refund_fee',
    'type' => 'float(8,2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'int(10)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);