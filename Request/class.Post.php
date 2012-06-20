<?php

namespace Xa\Request;

class Post extends Base
{

    protected static $_data = array();
    protected static $_type = 'post';

    public static function build()
    {
        static::$_data = $_POST;
    }

}

?>