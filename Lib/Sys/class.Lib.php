<?php
namespace Xa\Lib\Sys;


class Lib
{
    protected $_path;
    protected $_libFile;
    protected $_namespace;

    public function __construct($namespace)
    {
        $this->_namespace = $namespace;
        $this->_path = namespace2ADir($namespace, -1);
        if (!static::isLib($this->_path))
        {
            throw new \Exception("$namespace не является библиотекой. Т.к не найден файл " . \Xa\Path::absoluteToRelative($this->_path) . '/lib.php');
        }

        $this->_libFile = $this->_path . '/lib.php';
    }


    public function install()
    {
        include($this->_libFile);
        $params = call_user_func($this->_namespace . '\\install');
        if (call_user_func($this->_namespace . '\\installed'))
        {
            throw new \Exception('Already installed');
        }
        foreach ($params as $key => $param)
        {
            $key = ucfirst($key);
            if (method_exists($this, 'opser' . $key))
            {
                $this->{'oper' . $key}($param);
            }
        }
    }

    protected function operDepend(array $depends = array())
    {

    }

    protected function operQueries(array $queries = array())
    {
        $conn = \ActiveRecord\ConnectionManager::get_connection();
        try
        {
            foreach ($queries as $query)
            {
                $conn->query($query);
            }
        }
        catch (\Exception $e)
        {
            $this->uninstall();
        }
    }

    public static function isLib($path)
    {
        return file_exists($path . '/lib.php');
    }
}