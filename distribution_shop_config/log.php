<?php

return [
    // 日志记录方式，内置 file socket 支持扩展
    'type' => 'File',
    // 日志保存目录
    'path' => '/data/wwwlogs/distribution',
    // 日志记录级别
    'level' => [],

    'apart_level' => ['error', 'sql'],
    'max_files' => 30,
];