<?php

namespace Xa;

class Cache
{

    protected $_adapter;

    public function __construct($adapter)
    {
        $this->_adapter = $adapter;
    }

    public function set($var, $value, $lifetime)
    {
        $this->_adapter->set($var, $value, $lifetime);
    }

    public function get($var)
    {
        return $this->_adapter->get($var);
    }

}

?>