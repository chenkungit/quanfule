<?php 
return array (
  'article_id' => 
  array (
    'name' => 'article_id',
    'type' => 'mediumint(8) unsigned',
    'notnull' => false,
    'default' => NULL,
    'primary' => true,
    'autoinc' => true,
  ),
  'cat_id' => 
  array (
    'name' => 'cat_id',
    'type' => 'smallint(5)',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'wcat_id' => 
  array (
    'name' => 'wcat_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'title' => 
  array (
    'name' => 'title',
    'type' => 'varchar(150)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'content' => 
  array (
    'name' => 'content',
    'type' => 'longtext',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'content_json' => 
  array (
    'name' => 'content_json',
    'type' => 'text',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'author' => 
  array (
    'name' => 'author',
    'type' => 'varchar(30)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'author_email' => 
  array (
    'name' => 'author_email',
    'type' => 'varchar(60)',
    'notnull' => false,
    'default' => '',
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
  'article_type' => 
  array (
    'name' => 'article_type',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '2',
    'primary' => false,
    'autoinc' => false,
  ),
  'is_open' => 
  array (
    'name' => 'is_open',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '1',
    'primary' => false,
    'autoinc' => false,
  ),
  'add_time' => 
  array (
    'name' => 'add_time',
    'type' => 'int(10) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'file_url' => 
  array (
    'name' => 'file_url',
    'type' => 'varchar(2550)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'video_url' => 
  array (
    'name' => 'video_url',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'video_img' => 
  array (
    'name' => 'video_img',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'open_type' => 
  array (
    'name' => 'open_type',
    'type' => 'tinyint(1) unsigned',
    'notnull' => false,
    'default' => '0',
    'primary' => false,
    'autoinc' => false,
  ),
  'link' => 
  array (
    'name' => 'link',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => '',
    'primary' => false,
    'autoinc' => false,
  ),
  'description' => 
  array (
    'name' => 'description',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'label' => 
  array (
    'name' => 'label',
    'type' => 'varchar(255)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'label2' => 
  array (
    'name' => 'label2',
    'type' => 'varchar(40)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'topic_id' => 
  array (
    'name' => 'topic_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'zan' => 
  array (
    'name' => 'zan',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'user_id' => 
  array (
    'name' => 'user_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'p_article_id' => 
  array (
    'name' => 'p_article_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'share_num' => 
  array (
    'name' => 'share_num',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'view_num' => 
  array (
    'name' => 'view_num',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'top' => 
  array (
    'name' => 'top',
    'type' => 'int(4)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'ord' => 
  array (
    'name' => 'ord',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'real_user_id' => 
  array (
    'name' => 'real_user_id',
    'type' => 'int(8)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'weidu' => 
  array (
    'name' => 'weidu',
    'type' => 'varchar(1024)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'title_small' => 
  array (
    'name' => 'title_small',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
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
  'huichang' => 
  array (
    'name' => 'huichang',
    'type' => 'varchar(80)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'template_type' => 
  array (
    'name' => 'template_type',
    'type' => 'int(4)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'thumbnail_pic' => 
  array (
    'name' => 'thumbnail_pic',
    'type' => 'varchar(5120)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
  'original_pic' => 
  array (
    'name' => 'original_pic',
    'type' => 'varchar(5120)',
    'notnull' => false,
    'default' => NULL,
    'primary' => false,
    'autoinc' => false,
  ),
);