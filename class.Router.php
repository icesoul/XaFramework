<?php

namespace Xa;

class Router implements \Xa\Interfaces\Router
{


    protected $_url;
    protected $_destinations = array();
    protected $_controller;
    protected $_handler;
    protected $_parts;
    protected $_default;
    protected $_alias;
    protected $_current;

    public $Get;

    public function __construct(\Xa\Request\Interfaces\Get $Get)
    {

        $this->Get = $Get;
        $this->_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        if ($pos = strpos($this->_url, '?'))
        {
            parse_str(substr($this->_url, strpos($this->_url, '?') + 1), $getVars);
            $this->_url = substr($this->_url, 0, strpos($this->_url, '?'));
            $Get->sArray($getVars);
        }
        $this->_prepost = substr($this->_url, strlen(SITE) - 1);
        $this->_prepost = $this->_prepost[strlen($this->_prepost) - 1] == '/' ? substr($this->_prepost, 0, -1) : $this->_prepost;
        $this->_prepost = empty($this->_prepost) ? '/' : $this->_prepost;
    }

    public function addDestination($prepost, $controller)
    {
        $destinations = &$this->_destinations;

        $calcIndex = function($i) use (&$destinations, &$calcIndex)
        {
            if (isset($destinations[$i]))
            {
                $i++;
                return $calcIndex($i);
            }
            return $i;
        };
        $index = $calcIndex(strlen($prepost));
        $this->_destinations[$index] = array('prepost' => $prepost, 'controller' => $controller);
        $this->_alias[$controller] = $this->_destinations[$index];
        return $index;
    }

    public function setDefaultDestination($prepost, $controller)
    {
        $this->_default = $this->addDestination($prepost, $controller);
    }


    public function route()
    {

        $called = false;
        ksort($this->_destinations);


        foreach (array_reverse($this->_destinations) as $dest)
        {
            if (strpos($this->_prepost, $dest['prepost']) !== false)
            {

                if ($this->call($dest) === false)
                {

                    continue;
                }
                else
                {

                    return true;
                }
            }
        }

        // $this->error404();
    }

    protected function call($dest)
    {
        $attributes = array();

        $prepost = substr($this->_prepost, strlen($dest['prepost']));
        $prepost = $prepost[0] == '/' ? substr($prepost, 1) : $prepost;
        $parts = explode('/', $prepost);
        $handler = array_shift($parts);
        $handler = $handler ? : 'index';
        if (!class_exists($dest['controller']))
        {
            return false;
        }

        // Registry::Callback()->invoke('beforeCreateControllerClass', array(&$prepost, &$parts, &$handler, $dest));

        $reflector = new \ReflectionClass($dest['controller']);

        if ($reflector->hasMethod('controller_' . $handler))
        {
            $this->_current = $dest;
            $this->_handler = $handler;

            $this->_controller = $dest['controller']::create();
            $reflector = new \ReflectionClass($dest['controller']);
            $q = $reflector->getMethod('controller_' . $handler)->getParameters();

            $params = $reflector->getMethod('controller_' . $handler)->getParameters();
            //Registry::Callback()->invoke('beforeControllerValidation', array($reflector, &$params, &$parts));

            foreach ($params as $i => $param)
            {
                if (empty($parts[$i]) and !($canEmpty = $param->isDefaultValueAvailable()))
                {
                    return false;
                }

                $this->Get->s($param->name, !empty($parts[$i]) ? $parts[$i] : null);
                $attributes[] = $this->Get->g($param->name);
            }


            $this->_controller->preroute($handler);
            // Registry::Callback()->invoke('beforeCallControllerHandler', array(&$prepost, &$parts, &$handler, $dest));
            call_user_func_array(array($this->_controller, 'controller_' . $handler), $attributes);
            //Registry::Callback()->invoke('afterRoute', array(&$prepost, &$parts, &$handler, $dest));
        }
        else
        {
            return false;
        }
    }

    public function getByController($controller)
    {
        return array_key_exists($controller, $this->_alias) ? $this->_alias[$controller] : false;
    }

    public function getCurrent()
    {
        return $this->_current;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function getHandler()
    {

        return $this->_handler;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function getPrepost()
    {
        return $this->_prepost;
    }

    public function setPrepost($prepost)
    {
        $this->_prepost = $prepost;
    }


    /**
     * @static
     * @return Router
     */
    public static function create()
    {
        return IoC\Factory::create(get_called_class(), func_get_args());
    }

}

?>