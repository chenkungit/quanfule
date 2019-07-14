<?php 
return array (
  'rank_id' => 
  array (
    'name' => 'rank_id',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'rank_name' => 
  array (
    'name' => 'rank_name',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'min_points' => 
  array (
    'name' => 'min_points',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'max_points' => 
  array (
    'name' => 'max_points',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'discount' => 
  array (
    'name' => 'discount',
    'type' => 'tinyint(3) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'show_price' => 
  array (
    'name' => 'show_price',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'special_rank' => 
  array (
    'name' => 'special_rank',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'no_points' => 
  array (
    'name' => 'no_points',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'user_rank_type' => 
  array (
    'name' => 'user_rank_type',
    'type' => 'tinyint(2)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);