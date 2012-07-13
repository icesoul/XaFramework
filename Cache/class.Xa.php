<?php

namespace Xa\Cache;

class Xa implements \Xa\Interfaces\Cache
{


    protected $_destination;

    protected $_key;

    public function __construct($dest, $key)
    {
        $this->_destination = $dest;
        $this->_key = $key;
        if (!is_readable($this->_destination) or !is_writable($this->_destination))
        {
            throw new \Exception();
        }
    }

    public function clearAll()
    {
        foreach (new \DirectoryIterator($this->_destination) as $File)
        {
            if (!$File->isDot())
            {
                //echo $this->_destination;
                unlink($this->_destination . $File->getFilename());
                CacheTag::delete_all();
            }
        }
    }

    public function clear($var)
    {
        if (file_exists($p = $this->_destination . $var))
        {
            unlink($p);
            CacheTag::delete(array('var' => $var . $this->_key));
        }
    }

    public function clearByTags(array $tags)
    {
        foreach (CacheTag::all(array('conditions' => array('tag IN(?)', $tags))) as $relation)
        {
            $relation->delete();
            unlink($this->_destination . $relation->var);
            CacheTag::table()->conn->query("DELETE FROM `cache_tags` WHERE `var`=\"$relation->var\"");
        }
    }

    public function set($var, $value, $lifetime, array $tags = array())
    {
        //     return;
        $var = $var . $this->_key;
        $path = $this->_destination . $var;
        $fp = fopen($this->_destination . $var, 'wb');

        fwrite($fp, is_array($value) || is_object($value) ? serialize($value) : $value);
        fclose($fp);
        chmod($path, 0777);
        touch($this->_destination . $var, time() + $lifetime);

        if ($tags)
        {
            foreach ($tags as $tag)
            {
                CacheTag::create(array('var' => $var, 'tag' => $tag));
            }
        }
    }

    public function get($var, $type = self::AsString)
    {
        $var .= $this->_key;
        $cur = time();
        $p = $this->_destination . $var;

        if (!file_exists($p))
        {
            return false;
        }
        if (filemtime($p) < $cur)
        {
            $this->clear($var);
            return false;
        }

        $c = file_get_contents($p);
        $c = $type === static::AsArray ? unserialize($c) : $c;
        return $c;
    }

}

?>