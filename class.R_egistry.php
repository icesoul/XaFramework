<?php

namespace Xa;

class Registry
{

    protected static $_safeId = array();
    protected static $_data = array();

    protected function __construct ()
    {
        
    }

    protected function __clone ()
    {
        
    }

    public static function get ($var)
    {
        if (isset(self::$_data[$var]))
        {
            return self::$_data[$var];
        }

        throw new Exceptions\RegistryEntryNotFound('Entry: ' . $var . ' not found in registry');
    }

    public static function registered ($var)
    {
        return isset(static::$_data[$var]);
    }

    public static function set ($var, $value)
    {
        return self::$_data[$var] = $value;
    }

    public static function safeId ($class)
    {
        $name = get_class($class);
        if ( ! isset(static::$_safeId[$name]))
        {
            static::$_safeId[$name] = 0;
        }

        static::$_safeId[$name] ++;
        return static::$_safeId[$name];
    }

    public static function assign (array $names, array $classes)
    {
        //fixit
    }

    public static function __callStatic ($var, $attrs)
    {
        return static::get($var);
    }

}

?>