<?php

namespace Xa\Request;

abstract class Base
{

    const integer = '';
    const number = '';
    const string = '';
    const allowHtml = '';
    const filename = '|^[a-zA-Z0-9_\.]+$|';
    const basestring = '/^[a-zA-Zа-яА-Я0-9-_ \!\?\&;\.,]+$/u';
    const email = '/^[A-z0-9_\-\.]+\@[A-z0-9_-]+\.[A-z]{2,4}$/';

    protected static $_data = array();

    /**
     *
     * @param string $index
     * @return \Core\RequestSpeller
     */
    public static function g ($index)
    {
        return new Speller(isset(static::$_data[$index]) ? static::$_data[$index] : '', $index);
    }

    public static function gAsArray ($index, $handler = null)
    {
        $data = array();
        if (isset(static::$_data[$index]) and \is_array(static::$_data[$index]))
        {
            $type = static::$_type;

            $data = static::$_data[$index];
            /* \array_walk_recursive(&$data, function(&$v, $key) use ($index, $type, $handler)
              {
              $v = new Speller($v, $key, $type);
              $v = $handler ? call_user_func($handler, $v) : $v;
              });

              $to = array(); */
            $b = function($arr) use (&$b, $handler, $type)
                    {
                        $return = array();
                        foreach ($arr as $key => $val)
                        {
                            if (is_array($val))
                            {
                                $return[$key] = $b($val);
                            }
                            else
                            {
                                $v = new Speller($val, $key, $type);
                                $return[$key] = $handler ? call_user_func($handler, $v) : $v;
                            }
                        }

                        return $return;
                    };

            $to = $b(static::$_data[$index]);
            return $to;

        }
        throw new \Exception('Index ' . $index . ' not found');
    }

    public static function totality (array $indexes)
    {
        foreach ($indexes as $index)
        {
            if ( ! \array_key_exists($index, static::$_data))
                return false;
        }

        return true;
    }

    public static function exists ($index)
    {
        return \array_key_exists($index, static::$_data);
    }

    public static function isEmpty ($index)
    {
        return empty(static::$_data[$index]);
    }

    public static function getAll ()
    {
        return static::$_data;
    }

    public static function s ($index, $value)
    {
        static::$_data[$index] = $value;
    }

    public static function sArray ($data)
    {
        static::$_data = array_merge(static::$_data, $data);
    }

    public static function destroy ($index)
    {
        if (isset(static::$_data[$index]))
        {
            unset(static::$_data[$index]);
        }
    }

}

?>