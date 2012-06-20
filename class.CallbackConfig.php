<?php

namespace Xa;

class CallbackConfig
{

    protected $_setters = array();
    protected $_getters = array();
    protected $_config = array();

    public function set($index, $value)
    {

        foreach (!empty($this->_setters[$index]) ? $this->_setters[$index] : array() as $handler)
        {
            $value = call_user_func($handler, $value);
        }
        $cindex = ucfirst($index);
  
        if (method_exists($this, 'configure' . $cindex))
        {
            $value = $this->{'configure' . $cindex}($value);
        }
        return $this->_config[$index] = $value;
    }

    public function __set($index, $value)
    {
        return $this->set($index, $value);
    }

    public function __get($var)
    {
        return $this->_config[$var];
    }

    public function setter($handler, $indexes)
    {
        $indexes = is_array($indexes) ? $indexes : array($indexes);
        foreach ($indexes as $index)
        {
            $this->_setters[$index][] = $handler;
        }


        return $this;
    }

}

?>