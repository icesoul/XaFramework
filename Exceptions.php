<?php

namespace Xa\Exceptions;

use Exception;

class RequestDataIsNotString extends Exception
{

}

class InvalidModel extends Exception
{

    protected $_errors;

    public function __construct(\ActiveRecord\Errors $errors)
    {
        $this->_errors = $errors;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

}

class RequestInvalidData extends Exception implements \Iterator
{
    protected $_index;

    public function __construct($message = null, $code = null, $prev = null)
    {
        parent::__construct('Data is not valid: ' . $message, $code, $prev);
    }

    public function setIndex($method)
    {
        $this->_index = $method;
    }

    public function getIndex()
    {
        return $this->_index;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    // iterator
    public function rewind()
    {
        reset($this->_errors);
    }

    public function current()
    {
        return current($this->_errors);
    }

    public function key()
    {
        return key($this->_errors);
    }

    public function next()
    {
        return next($this->_errors);
    }

    public function valid()
    {
        $key = key($this->_errors);
        return ($key !== NULL && $key !== FALSE);
    }

}

class InstallationFileisNotValid extends \Exception
{

}

class ErrorCodeNotFound extends \Exception
{

}

class InvalidUrlVars extends \Exception
{

}

class RouteNotFound extends \Exception
{

}

class RegistryEntryNotFound extends Exception
{

}

class IncorrectFilePermissions extends \Exception
{

}

?>
