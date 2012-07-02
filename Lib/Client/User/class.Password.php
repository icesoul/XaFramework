<?php

namespace Xa\Lib\Client\User;


class Password
{
    protected $_pwd;

    public function __construct(\Xa\Lib\Protect\Crypt\Interfaces\Crypt $Mech, $str)
    {
        $this->_pwd = (string)$Mech->set($str);
    }

    public function __toString()
    {
        return $this->_pwd;
    }


    /**
     * @static
     * @return string
     */
    public static function create()
    {
        return \Xa\IoC\Factory::create(get_called_class(), func_get_args());
    }

}