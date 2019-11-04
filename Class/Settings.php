<?php

/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2018.08.14.
 * Time: 0:30
 */
class Settings extends Singleton {
    protected static $container = array();

    public static function set($key, $value, $module = 'default') {
        self::$container[strtolower($module)][$key] = $value;
    }

    public static function get($key, $module = 'default') {
        return @self::$container[strtolower($module)][$key];
    }
}