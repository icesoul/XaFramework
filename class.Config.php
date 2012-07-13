<?php

namespace Xa;

class Config implements \ArrayAccess
{
    const overload='overload';
    const lazyload='lazyload';
    const overlay='overlay';

    /**
     * Проще...а вообще баг сеттера
     */
    public $_data = array();
    protected $_id;

    public function __construct($options=null, $sections=false)
    {
        if (is_array($options))
            $this->_data = $options;
    }

    public function overload(array $data)
    {
        $this->_data = \array_merge($this->_data, $data);
        return $this;
    }

    public function lazyload(array $data)
    {
        $this->_data = \array_merge($data, $this->_data);
        return $this;
    }

    public function overlay(array $data)
    {
        self::cfgSplit($this->_data, $data);
        return $this;
    }

    public function offsetSet($offset, $value)
    {
        if (\is_null($offset))
            throw new \Exception('Для изменения конфигурации укажите ключ');

        $this->_data[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }

    public function __isset($var)
    {
        return isset($this->_data[$var]);
    }

    public function __get($var)
    {
        return isset($this->_data[$var]) ? $this->_data[$var] : false;
    }

    public function __set($var, $value)
    {
        $this->_data[$var] = $value;
    }

    public function all()
    {
        return $this->_data;
    }

    public function asJs()
    {
        return json_encode($this->_data);
    }

    public function getId()
    {
        return $this->_id;
    }

    public static function detect($options)
    {
        if (is_a($options, '\Xa\Config'))
        {
            return $options;
        }
        return new Config($options);
    }

    public static function cfgSplit(&$ar1, $ar2)
    {
        foreach ($ar2 as $k => $v)
        {
            if (isset($ar1[$k]))
            {
                if (is_array($ar1[$k]) and is_array($v))
                {
                    self::cfgSplit($ar1[$k], $v);
                    continue;
                }
                $ar1[$k] = $v;
                continue;
            }
            $ar1[$k] = $v;
        }
    }

}

?>