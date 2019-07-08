<?php

namespace app\service;

class BaseService
{
    private static $_services = [];
    private static $name;

    public static function service()
    {
        $name = get_called_class();
        self::$name = $name;
        if (!isset(self::$_services[$name]) || !is_object(self::$_services[$name])) {
            $instance = self::$_services[$name] = new static();
            return $instance;
        }
        return self::$_services[$name];
    }

    /**
     * 防止克隆
     */
    private function __clone()
    {
    }
}
