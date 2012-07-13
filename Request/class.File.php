<?php

namespace Xa\Request;

use SplFileInfo;

class File extends Base implements \Xa\Request\Interfaces\File
{

    protected $_data = array();
    protected $_type = 'file';

    public function __construct()
    {
        $this->_data = array();
        foreach ($_FILES as $firstNameKey => $arFileDescriptions)
        {
            foreach ($arFileDescriptions as $fileDescriptionParam => $mixedValue)
            {
                static::rRestructuringFilesArray($this->_data, $firstNameKey, $_FILES[$firstNameKey][$fileDescriptionParam], $fileDescriptionParam);
            }
        }

    }


    public function g($index)
    {
        return $this->_data[$index];
    }

    public function gAsArray($index, $handler = null)
    {

        $data = array();
        if (isset($this->_data[$index]) and \is_array($this->_data[$index]))
        {

            return $this->_data[$index];

        }
        throw new \Exception('Index ' . $index . ' not found');
    }

    public static function makeFrom(array &$arrayl)
    {
        return new Filer($arrayl['name'], $arrayl['type'], $arrayl['tmp_name'], $arrayl['error'], $arrayl['size']);
    }

    /**
     * http://www.php.net/manual/ru/reserved.variables.files.php#106558
     */
    public static function rRestructuringFilesArray(&$arrayForFill, $currentKey, $currentMixedValue, $fileDescriptionParam)
    {
        if (is_array($currentMixedValue))
        {
            foreach ($currentMixedValue as $nameKey => $mixedValue)
            {
                static::rRestructuringFilesArray($arrayForFill[$currentKey], $nameKey, $mixedValue, $fileDescriptionParam);
            }
        }
        else
        {
            if (empty($arrayForFill[$currentKey]))
            {
                $arrayForFill[$currentKey] = new Filer();
                $arrayForFill[$currentKey]->setIndex($currentKey);
            }
            $arrayForFill[$currentKey]->set($fileDescriptionParam, $currentMixedValue);
        }
    }

}

class Filer implements \Xa\Request\Interfaces\Filer
{

    protected $msg = array(1 => 'File is too long', 2 => 'File is too long', 3 => 'File was not completely downloaded', 4 => 'File not loaded', 5 => 'Server tmp folder not found', 6 => 'Server error,write failed', 7 => 'Php остановил uploading,check php ext',);
    protected $_indexes = array('name', 'type', 'tmp_name', 'error', 'size');
    protected $_index;
    protected $_name;
    protected $_type;
    protected $_error;
    protected $_tmp_name;
    protected $_size;
    protected $_ext;

    public function __construct($name = null, $type = null, $tmp_name = null, $error = null, $size = null)
    {
        $this->_name = $name;
        $this->_type = $type;
        $this->_error = $error;
        $this->_value = $this->_tmp_name = $tmp_name;
        $this->_size = $size;
        $this->_ext = $this->_name ? pathinfo($this->_name, PATHINFO_EXTENSION) : false;

        //   $this->_translate = $this->_index = $index;
    }

    public function isValid()
    {
        if ($this->_error != self::UPLOAD_ERR_OK and $this->_error != self::UPLOAD_ERR_NO_FILE)
        {
            $this->throwon($this->_index, self::$msg[$this->_error]);
        }

        return $this;
    }

    public function allowExtensions($allow)
    {
        if (!is_array($allow))
        {
            $allow = array($allow);
        }

        if (count(array_intersect(array($this->getExt()), $allow)) > 0)
        {
            return $this;
        }

        $this->throwon($this->_index, 'extension is not supported');
    }

    public function denyExtensions($deny)
    {
        if (!is_array($deny))
        {
            $deny = array($deny);
        }

        if (count(array_intersect(array($this->_ext), $deny)) == 0)
        {
            return $this;
        }

        $this->throwon($this->_index, 'extension is not supported');
    }

    public function maxSize($size, $type = self::megabyte)
    {
        if ($this->_size <= $size * $type)
        {
            return $this;
        }
        $this->throwon($this->_index, 'big size');
    }

    public function setIndex($index)
    {
        $this->_index = $index;
    }

    public function validImg()
    {
        if (getimagesize($this->_tmp_name))
        {
            return $this;
        }
        $this->throwon($this->_index, 'is not a image');
    }

    public function isEmpty()
    {
        return $this->_error == self::UPLOAD_ERR_NO_FILE;
    }

    public function save($dir, $chmod = 0777)
    {
        $ext = $this->getExt();
        $gen = function() use (&$gen, $dir, $ext)
        {
            $name = $dir . uniqid() . '.' . $ext;
            if (file_exists($name))
            {
                return $gen();
            }
            return $name;
        };
        $name = $gen();

        if (move_uploaded_file($this->_tmp_name, $name))
        {

            chmod($name, $chmod);
            return $name;
        }

        $this->throwon($this->_index, 'file cant be saved');
    }

    public function set($index, $val)
    {

        if (in_array($index, $this->_indexes))
        {
            $this->{'_' . $index} = $val;

        }
    }

    public function getExt()
    {
        return pathinfo($this->_name, PATHINFO_EXTENSION);
    }

    public function throwon($index, $error)
    {

        $ex = new \Xa\Exceptions\RequestInvalidData($error);
        $ex->setIndex($index);
        throw $ex;
    }

}

?>