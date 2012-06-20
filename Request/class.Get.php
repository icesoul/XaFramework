<?php

namespace Xa\Request;

class Get extends Base
{

    protected static $_data = array();
    protected static $_type = 'get';

    public static function build(array $arr=array())
    {
        static::$_data = $arr ? : $_GET;
    }

}

?>
