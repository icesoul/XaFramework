<?php

namespace Xa;

class Callback implements \Xa\Interfaces\Callback
{

    protected $_callbacks = array();
    protected $_installed = array();

    public function __construct()
    {

    }

    public function invoke($event, array $attrs = array())
    {
        if (isset($this->_callbacks[$event]))
        {
            foreach ($this->_callbacks[$event] as $callback)
            {
                call_user_func_array($callback, $attrs);
            }
        }
        $this->callFromFileStorage($event, $attrs);
        return $this;
    }


    public function moreInvoke(array $events, array $attrs = array())
    {
        foreach ($events as $event)
        {
            $this->invoke($event, $attrs);
        }

        return $this;
    }

    public function register($event, $callback)
    {
        if (isset($this->_callbacks[$event]))
        {
            $this->_callbacks[$event][] = $callback;
        }
        else
        {
            $this->_callbacks[$event] = array($callback);
        }

        return $this;
    }

    public function handlersIsExists($event)
    {
        return isset($this->_callbacks[$event]);
    }

    public function __set($event, $callback)
    {
        $this->register($event, $callback);
    }

}

?>