<?php 
return array (
  'cat_id' => 
  array (
    'name' => 'cat_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'cat_name' => 
  array (
    'name' => 'cat_name',
    'type' => 'varchar(90)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'mobile_icon' => 
  array (
    'name' => 'mobile_icon',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'pc_icon' => 
  array (
    'name' => 'pc_icon',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'swiper' => 
  array (
    'name' => 'swiper',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'keywords' => 
  array (
    'name' => 'keywords',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'cat_desc' => 
  array (
    'name' => 'cat_desc',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'parent_id' => 
  array (
    'name' => 'parent_id',
    'type' => 'smallint(5) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'sort_order' => 
  array (
    'name' => 'sort_order',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '50',
    'primary' => false,
    'autoinc' => false,
  ),
  'template_file' => 
  array (
    'name' => 'template_file',
    'type' => 'varchar(50)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'measure_unit' => 
  array (
    'name' => 'measure_unit',
    'type' => 'varchar(15)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'show_in_nav' => 
  array (
    'name' => 'show_in_nav',
    'type' => 'tinyint(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'style' => 
  array (
    'name' => 'style',
    'type' => 'varchar(150)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_show' => 
  array (
    'name' => 'is_show',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'grade' => 
  array (
    'name' => 'grade',
    'type' => 'tinyint(4)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'filter_attr' => 
  array (
    'name' => 'filter_attr',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'img' => 
  array (
    'name' => 'img',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'imgurl' => 
  array (
    'name' => 'imgurl',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'goods_zt' => 
  array (
    'name' => 'goods_zt',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'Tgoodimg' => 
  array (
    'name' => 'Tgoodimg',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'Tgoodimgurl' => 
  array (
    'name' => 'Tgoodimgurl',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'Tgoodtext' => 
  array (
    'name' => 'Tgoodtext',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'Bgoodimg' => 
  array (
    'name' => 'Bgoodimg',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'redirect_id' => 
  array (
    'name' => 'redirect_id',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'redirect_type' => 
  array (
    'name' => 'redirect_type',
    'type' => 'tinyint(3)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'redirect_name' => 
  array (
    'name' => 'redirect_name',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'Bgoodimgurl' => 
  array (
    'name' => 'Bgoodimgurl',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'show_index' => 
  array (
    'name' => 'show_index',
    'type' => 'int(1)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'index_zu' => 
  array (
    'name' => 'index_zu',
    'type' => 'int(4)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'index_links' => 
  array (
    'name' => 'index_links',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);