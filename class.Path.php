<?php

namespace Xa;

class Path
{

    private $_relativePath;
    private $_absolutePath;
    private $_webPath;

    public function __construct ($path)
    {
        $path = str_replace("\\", '/', $path);
        if (strpos($path, 'http://') !== false)
        {
            $this->_webPath = $path;
            $this->_absolutePath = static::webToAbsolute($path);
        }
        elseif ($path[0] == '/')
        {
            $this->_absolutePath = $path;
        }

        $this->_path = $path;
    }

    public function getRelative ($start)
    {
        return static::absoluteToRelative($this->_absolutePath, $start);
    }

    public function getAbsolute ()
    {
        return $this->_absolutePath;
    }

    public function getWeb ()
    {
        return SITE . substr($this->getAbsolute(), strlen(AP));
    }

    public static function webToAbsolute ($webPath)
    {
        return AP . substr($webPath, strlen(SITE));
    }

    public static function relativeToAbsolute ($relativePath)
    {
        return AP . substr($relativePath, 1);
    }

    public static function absoluteToRelative ($absolutePath, $start = null)
    {
        $start = $start ? : AP;
        $l = strlen($start);

        return substr($absolutePath, $l !== false ? $l : 0);
    }

    public static function absoluteToWeb ($absolutePath, $start = null)
    {
        return SITE . static::absoluteToRelative($absolutePath, $start);
    }

    public function __toString ()
    {
        return $this->getAbsolute();
    }

}

?>