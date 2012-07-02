<?php

namespace Xa\Request;

class Cookie extends Base
{

    protected  $_data = array();
    protected  $_type = 'cookie';

    public function __construct()
    {
        $this->_data = $_COOKIE;
    }


    public  function s($index, $value)
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