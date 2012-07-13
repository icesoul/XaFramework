<?php

namespace Xa\Request;

use Xa;

class Speller extends Xa\Validator
{

    protected $_index;
    public $value;

    public function __construct($value, $index)
    {
        if (is_array($value) or \is_object($value))
        {
            throw new Xa\Exceptions\RequestDataIsNotString();
        }

        $this->_value = $value;
        $this->_translate = $this->_index = $index;
    }

    public function noEmpty()
    {
        if (!empty($this->_value))
        {
            return $this;
        }

        $this->throwon($this->_index, 'обязательно для заполнения');
    }

    public function canEmpty()
    {
        if (empty($this->_value))
        {
            return new EmptySpeller ();
        }

        return $this;
    }


}

?>
