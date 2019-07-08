<?php
namespace think;

define('CONFIG_PATH', realpath(dirname(__FILE__) . '/../../distribution_shop_config'));

define('UPLOAD_PATH', __DIR__ . '/static/');

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象
\think\facade\Log::close();
// 执行应用并响应
Container::get('app')->run()->send();

