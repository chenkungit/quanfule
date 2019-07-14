<?php 
return array (
  'delivery_id' => 
  array (
    'name' => 'delivery_id',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'order_id' => 
  array (
    'name' => 'order_id',
    'type' => 'int(11) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'invoice_no' => 
  array (
    'name' => 'invoice_no',
    'type' => 'varchar(50)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'rec_ids' => 
  array (
    'name' => 'rec_ids',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'logistics_type' => 
  array (
    'name' => 'logistics_type',
    'type' => 'varchar(5)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'logistics_name' => 
  array (
    'name' => 'logistics_name',
    'type' => 'varchar(20)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'is_part_sync' => 
  array (
    'name' => 'is_part_sync',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'consign_time' => 
  array (
    'name' => 'consign_time',
    'type' => 'timestamp',
    'notnull' => false,
    'default' => 'CURRENT_TIMESTAMP',
    'primary' => false,
    'autoinc' => false,
  ),
);