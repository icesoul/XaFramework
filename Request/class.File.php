<?php

namespace Xa\Request;

use SplFileInfo;

class File extends Base
{

    protected static $_data = array();
    protected static $_type = 'file';

    public static function build ()
    {
        static::$_data = array();
        foreach ($_FILES as $firstNameKey => $arFileDescriptions)
        {
            foreach ($arFileDescriptions as $fileDescriptionParam => $mixedValue)
            {
                static::rRestructuringFilesArray(static::$_data, $firstNameKey, $_FILES[$firstNameKey][$fileDescriptionParam], $fileDescriptionParam);
            }
        }
    }

    public static function g ($index)
    {
        return self::$_data[$index];
    }

    public static function gAsArray ($index, $handler = null)
    {

        var_dump(self::$_data);
        die();
        $data = array();
        if (isset(static::$_data[$index]) and \is_array(static::$_data[$index]))
        {
            $type = static::$_type;

            $data = static::$_data[$index];

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

            $to = $b(static::$_data[$index]);
            return $to;
        }
        throw new \Exception('Index ' . $index . ' not found');
    }

    public static function makeFrom (array &$arrayl)
    {
        return new Filer(
                        $arrayl['name'],
                        $arrayl['type'],
                        $arrayl['tmp_name'],
                        $arrayl['error'],
                        $arrayl['size']
        );
    }

    /**
     * http://www.php.net/manual/ru/reserved.variables.files.php#106558
     */
    public static function rRestructuringFilesArray (&$arrayForFill, $currentKey, $currentMixedValue, $fileDescriptionParam)
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
            }
            $arrayForFill[$currentKey]->set($fileDescriptionParam, $currentMixedValue);
        }
    }

}

class Filer
{

    const bytes = 1;
    const kilobyte = 1024;
    const megabyte = 1048576;
    const gigabyte = 1073741824;
    const UPLOAD_ERR_OK = 0;
    const UPLOAD_ERR_INI_SIZE = 1;
    const UPLOAD_ERR_FORM_SIZE = 2;
    const UPLOAD_ERR_PARTIAL = 3;
    const UPLOAD_ERR_NO_FILE = 4;
    const UPLOAD_ERR_NO_TMP_DIR = 5;
    const UPLOAD_ERR_CANT_WRITE = 6;
    const UPLOAD_ERR_EXTENSION = 7;

    static $msg = array(
        1 => 'File is too long',
        2 => 'File is too long',
        3 => 'File was not completely downloaded',
        4 => 'File not loaded',
        5 => 'Server tmp folder not found',
        6 => 'Server error,write failed',
        7 => 'Php остановил uploading,check php ext',
    );
    static $_indexes = array(
        'name', 'type', 'tmp_name', 'error', 'size'
    );
    protected $_index;
    protected $_name;
    protected $_type;
    protected $_error;
    protected $_tmp_name;
    protected $_size;
    protected $_ext;

    public function __construct ($name = null, $type = null, $tmp_name = null, $error = null, $size = null)
    {
        $this->_name = $name;
        $this->_type = $type;
        $this->_error = $error;
        $this->_value = $this->_tmp_name = $tmp_name;
        $this->_size = $size;
        $this->_ext = $this->_name ? pathinfo($this->_name, PATHINFO_EXTENSION) : null;

        //   $this->_translate = $this->_index = $index;
    }

    public function isValid ()
    {
        if ($this->_error != self::UPLOAD_ERR_OK and $this->_error != self::UPLOAD_ERR_NO_FILE)
        {
            $this->throwon($this->_index, self::$msg[$this->_error]);
        }

        return $this;
    }

    public function allowExtensions ($allow)
    {
        if ( ! is_array($allow))
        {
            $allow = array($allow);
        }


        if (count(array_intersect(array($this->_ext), $allow)) > 0)
            return $this;

        $this->throwon($this->_index, 'расширение файла не поддерживается');
    }

    public function denyExtensions ($deny)
    {
        if ( ! is_array($deny))
        {
            $deny = array($deny);
        }

        if (count(array_intersect(array($this->_ext), $deny)) == 0)
            return $this;

        $this->throwon($this->_index, 'расширение файла не поддерживается');
    }

    public function maxSize ($size, $type = self::megabyte)
    {
        if ($this->_size <= $size * $type)
        {
            return $this;
        }
        $this->throwon($this->_index, 'слишком большой размер файла');
    }

    public function validImg ()
    {
        if (getimagesize($this->_tmp_Name))
        {
            return $this;
        }
        $this->throwon($this->_index, 'это не изображение');
    }

    public function isEmpty ()
    {
        return $this->_error == self::UPLOAD_ERR_NO_FILE;
    }

    public function save ($dir)
    {
        $ext = $this->getExt();
        $gen = function() use (&$gen, $dir, $ext)
                {
                    $name = $dir . uniqid() . '.' . $ext;
                    if (file_exists($name))
                    {
                        return $save();
                    }
                    return $name;
                };
        $name = $gen();

        if (move_uploaded_file($this->_tmp_name, $name))
        {
            return $name;
        }

        $this->throwon($this->_index, 'не удалось сохранить файл');
    }

    public function set ($index, $val)
    {
        
        if (in_array($index, static::$_indexes))
        {
            $this->{'_' . $index} = $val;
            
        }
    }

    public function getExt ()
    {
        return pathinfo($this->_name, PATHINFO_EXTENSION);
    }

    public function throwon ($index, $error)
    {
        throw new \Xa\Exceptions\RequestInvalidData($error);
    }

}

?>