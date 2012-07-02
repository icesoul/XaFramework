<?php

namespace Xa;

class Callback
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

    public function save2File($event, $callback)
    {
        $st = new CallbackStorage($event);
        $st->add($callback);
    }

    public function callFromFileStorage($event, array $attrs = array())
    {
        if (isset($this->_installed[$event]))
        {
            foreach (include(__DIR__ . '/Callbacks/' . $event . '.php') as $callback)
            {
                $result = call_user_func_array($callback, $attrs);
            }
        }

        return $this;
    }

    public function __set($event, $callback)
    {
        $this->register($event, $callback);
    }

}

?>