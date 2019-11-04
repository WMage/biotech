<?php

/**
 * Created by PhpStorm.
 * User: White Mage
 * Date: 2018.08.13.
 * Time: 20:40
 */
class Singleton {
    private static $instances = array();
    protected $className;

    protected function __construct() {
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public static function getInstance($name = '', $params = array()) {
        if (($callerName = get_called_class()) != get_class()) {
            $params = $name;
            $name = $callerName;
        }
        $class = &self::$instances[$name];
        if (!isset(self::$instances[$name])) {
            if (empty($params)) {
                self::$instances[$name] = new $name();
            } else {
                //call_user_func_array( array( $name, 'lists' ), $params);
                self::$instances[$name] = new $name(...$params);
            }
            if (self::$instances[$name] instanceof self) {
                $class->className = $name;
            }
            $initMethod = "Load";
            if (method_exists($class, $initMethod)) {
                $methodData = new ReflectionMethod($class, $initMethod);
                if ($methodData->isStatic()) {
                    $class::$initMethod();
                } else {
                    self::$instances[$name]->$initMethod();
                }
            }
        }
        return self::$instances[$name];
    }
}