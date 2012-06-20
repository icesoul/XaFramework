<?php

namespace Xa\Request;

class Cookie extends Base
{

    protected static $_data = array();
    protected static $_type = 'cookie';

    public static function build()
    {

        static::$_data = $_COOKIE;
    }


    public static function s($index, $value)
    {
        return;
    }

    public static function send($index, $value, $expire = 0, $path = '/', $domain = false, $secure = false, $http = false)
    {
        $domain = $domain ? : $_SERVER['HTTP_HOST'];
        return \setcookie($index, $value, $expire, $path, $_SERVER['HTTP_HOST'] == 'localhost' ? false : $domain, $secure, $http);
    }
}

?>