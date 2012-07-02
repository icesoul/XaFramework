<?php

namespace Xa;

class Validator
{

    protected static $_errors;
    protected $_value;
    protected $_translate;
    protected static $_suppress = false;

    public function __construct($value)
    {
        $this->_value = $value;
      //  $this->_translate = $this->_index = $index;
    }

    public function safe()
    {
        $this->_value = trim(
            \str_replace(array("'", "\"", "`", "\r", "\n"), array('&quot;', '&#x27;', '&#x60;', '', ''), \htmlspecialchars($this->_value)));

        return $this;
    }

    public function safeMultiline()
    {
        $this->_value = trim(
            \str_replace(array("'", "\"", "`"), array('&quot;', '&#x27;', '&#x60;', '', ''), \htmlspecialchars($this->_value)));
        return $this;
    }

    public function safeHtml($allowTags = null)
    {
        $this->_value = \str_replace(array("'", "\"", "`"), array('&quot;', '&#x27;', '&#x60;', '', ''), $this->_value);
        $this->_value = $allowTags ? strip_tags($this->_value, $allowTags) : $allowTags;


        return $this;
    }

    public function isEmail()
    {
        if (!preg_match('|^([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $this->_value))
        {
            $this->throwon($this->_index, 'это не email');
        }

        return $this;
    }

    public function isUrl()
    {
        if (filter_var($this->_value, FILTER_VALIDATE_URL))
        {
            return $this;
        }

        $this->throwon($this->_index, 'это не url');
    }

    /**
     *
     * @param string $pattern
     *
     * @return \Core\RequestSpeller
     */
    public function vRegexp($pattern)
    {
        if ($this->_value and \preg_match($pattern, $this->_value))
        {
            return $this;
        }

        $this->throwon($this->_index, 'не соответствует формату');
    }

    public function float()
    {
        $this->_value = \str_replace(array(' ', ','), array('', '.'), $this->_value);
        if (\preg_match('/^\d{1,10}.?\d{0,10}$/', $this->_value))
        {
            return $this;
        }

        $this->throwon($this->_index, 'не число');
    }

    /**
     *
     * @return \Core\RequestSpeller
     */
    public function vNumber()
    {
        if (\ctype_digit($this->_value))
        {
            return $this;
        }
        $this->throwon($this->_index, 'это не число ');
    }

    public function isEmpty()
    {
        return empty($this->_value);
    }

    /**
     *
     * @return \Core\RequestSpeller
     */
    public function range($min, $max)
    {
        if (!empty($this->_value))
        {
            $s = \strlen($this->_value);

            if ($s > $max or $s < $min)
            {
                $this->throwon($this->_index, 'длина строки должна быть в диапазоне от ' . $min . ' до ' . $max . ' символов');
            }
        }
        return $this;
    }

    public function length($len)
    {
        if (!empty($this->_value) and \strlen($this->_value) != $len)
        {
            $this->throwon($this->_index, 'длина строки должна быть равна ' . $len);
        }
        return $this;
    }

    public function lb($min, $max)
    {
        if ($this->_value > $max or $this->_value < $min)
        {
            $this->throwon($this->_index, " должен быть больше $min и меньше $max");
        }
        return $this;
    }

    /**
     *
     * @return \Core\RequestSpeller
     */
    public function min($min)
    {
        if (!empty($this->_value))
        {
            if (\strlen($this->_value) < $min)
            {
                $this->throwon($this->_index, 'минимальное кол-во символов: ' . $min);
            }
        }
        return $this;
    }

    /**
     *
     * @return \Core\RequestSpeller
     */
    public function max($max)
    {
        if (!empty($this->_value))
        {
            if (\strlen($this->_value) > $max)
            {
                $this->throwon($this->_index, 'максимальное кол-во символов: ' . $max);
            }
        }
        return $this;
    }

    public function toValue($value)
    {
        if (!empty($this->_value))
        {
            if ($this->_value != $value)
            {
                $this->throwon($this->_index, 'не совпадает');
            }
        }
        return $this;
    }

    public function oneOfMany(array $values)
    {
        if (\in_array($this->_value, $values) === false)
        {
            $this->throwon($this->_index, 'notValid');
        }


        return $this;
    }

    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function throwon($index, $error)
    {
        if (static::$_suppress)
        {
            static::$_errors[] = $this->_translate . ' - ' . $error;
            return;
        }
        throw new Exceptions\RequestInvalidData($this->_translate . ' - ' . $error);
    }

    public function setIndex($index)
    {
        $this->_index = $index;
    }

    public function getIndex()
    {
        return $this->_index;
    }

    public function translate($translate)
    {
        $this->_translate = $translate;
        return $this;
    }


    public function getValue()
    {
        return (string)$this->_value;
    }


    public function __toString()
    {
        return (string)$this->_value;
    }

    public static function suppress()
    {
        static::$_suppress = true;
    }

    public static function flush()
    {
        static::$_suppress = false;

        if (static::$_errors)
        {
            throw new Exceptions\RequestInvalidData(2);
        }
    }

}

?>
