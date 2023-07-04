<?php

namespace Hatch\Nacos\Service;

use \Exception;

class Singleton
{
    /**
     * initialize function name
     * @var string
     */
    protected static $init_func_name = '_initialize';

    /**
     * The actual singleton's instance almost always resides inside a static
     * field. In this case, the static field is an array, where each subclass of
     * the Singleton stores its own instance.
     */
    private static $instances = [];

    /**
     * Singleton's constructor should not be public. However, it can't be
     * private either if we want to allow subclassing.
     */
    protected function __construct() { }

    /**
     * Cloning and deserialization are not permitted for singletons.
     */
    protected function __clone() { }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot deserialize singleton");
    }

    /**
     * The method you used to get the Singleton's instance.
     */
    public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
            method_exists($subclass, $subclass::$init_func_name) && call_user_func([$subclass, $subclass::$init_func_name]);
        }
        return self::$instances[$subclass];
    }
}
