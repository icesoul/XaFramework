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

    protected $_data = array();

    /**
     *
     * @param string $index
     * @return \Core\RequestSpeller
     */
    public function g($index)
    {
        return new Speller(isset($this->_data[$index]) ? $this->_data[$index] : '', $index);
    }

    public function gAsArray($index, $handler = null)
    {
        $data = array();
        if (isset($this->_data[$index]) and \is_array($this->_data[$index]))
        {
            $type = $this->_type;

            $data = $this->_data[$index];
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

            $to = $b($this->_data[$index]);
            return $to;

        }
        throw new \Exception('Index ' . $index . ' not found');
    }

    public function totality(array $indexes)
    {
        foreach ($indexes as $index)
        {
            if (!\array_key_exists($index, $this->_data))
            {
                return false;
            }
        }

        return true;
    }

    public function exists($index)
    {
        return \array_key_exists($index, $this->_data);
    }

    public function isEmpty($index)
    {
        return empty($this->_data[$index]);
    }

    public function getAll()
    {
        return $this->_data;
    }

    public function s($index, $value)
    {
        $this->_data[$index] = $value;
    }

    public function sArray($data)
    {
        $this->_data = array_merge($this->_data, $data);
    }

    public function destroy($index)
    {
        if (isset($this->_data[$index]))
        {
            unset($this->_data[$index]);
        }
    }

}

?>