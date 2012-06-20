<?php

namespace Xa\Helper;

class SuppressedStorage implements \Iterator
{
    protected $_data;

    public function __construct(array $data = array())
    {
        $this->_data = $data;
    }

    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : false;
    }

    // iterator
    public function rewind()
    {
        reset($this->_data);
    }

    public function current()
    {
        return current($this->_data);
    }

    public function key()
    {
        return key($this->_data);
    }

    public function next()
    {
        return next($this->_data);
    }

    public function valid()
    {
        $key = key($this->_data);
        return ($key !== NULL && $key !== FALSE);
    }
}

?>