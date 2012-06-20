<?php

namespace Xa\Lib\Data;

use Iterator;
use Countable;

class Breadcrumbs implements Iterator, Countable
{

    protected $_chain = array();

    public function append ($url, $title)
    {
        $this->_chain[] = array($url, $title);
        return $this;
    }

    public function appendCurrent ()
    {
        $r = \Xa\Registry::Router();
        $ctrl = $r->getController();
        return $this->append($r->getUrl(), $ctrl::$titles[$r->getHandler()]);
    }

    public function appendFromController ($controllerClass, $controllerName, array $attrs = array(), $customTitle = null)
    {
        $title = $customTitle? : $this->makeTitle($controllerClass, $controllerName);
        $url = call_user_func($controllerClass . '::url' . ucfirst($controllerName), $attrs);
        return $this->append($url, $title);
    }

    public function makeTitle ($class, $h)
    {
        return property_exists($class, 'titles') && isset($class::$titles[$h]) ? $class::$titles[$h] : $h;
    }

    public function count ()
    {
        return count($this->_chain);
    }

    // iterator 
    public function rewind ()
    {
        reset($this->_chain);
    }

    public function current ()
    {
        return current($this->_chain);
    }

    public function key ()
    {
        return key($this->_chain);
    }

    public function next ()
    {
        return next($this->_chain);
    }

    public function valid ()
    {
        $key = key($this->_chain);
        return ($key !== NULL && $key !== FALSE);
    }

}

?>
