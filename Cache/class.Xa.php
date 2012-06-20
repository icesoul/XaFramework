<?php

namespace Xa\Cache;

class Xa
{

    const AsArray = 1;
    const AsString = 0;

    protected $_destination;

    public function __construct (\Xa\Config $attrs)
    {
        $this->_destination = $attrs->destination;
        if ( ! is_readable($this->_destination) or ! is_writable($this->_destination))
        {
            //   throw new \Exception();
        }
    }

    public function set ($var, $value, $lifetime, array $tags = array())
    {

        $path = $this->_destination . $var;
        $fp = fopen($this->_destination . $var, 'wb');

        fwrite($fp, is_array($value) || is_object($value) ? serialize($value) : $value);
        fclose($fp);

        chmod($path, 0777);
        touch($this->_destination . $var, time() + $lifetime);
    }

    public function get ($var, $type = self::AsString)
    {
        $cur = time();
        $p = $this->_destination . $var;

        if ( ! file_exists($p))
        {
            return false;
        }
        if (filemtime($p) < $cur)
        {
            unlink($p);
            return false;
        }

        $c = file_get_contents($p);
        $c = $type === static::AsArray ? unserialize($c) : $c;
        return $c;
    }

}

?>
