<?php

namespace Xa;

class Router
{

    const
        HTTP_100 = 'Continue',
        HTTP_101 = 'Switching Protocols',
        HTTP_200 = 'OK',
        HTTP_201 = 'Created',
        HTTP_202 = 'Accepted',
        HTTP_203 = 'Non-Authorative Information',
        HTTP_204 = 'No Content',
        HTTP_205 = 'Reset Content',
        HTTP_206 = 'Partial Content',
        HTTP_300 = 'Multiple Choices',
        HTTP_301 = 'Moved Permanently',
        HTTP_302 = 'Found',
        HTTP_303 = 'See Other',
        HTTP_304 = 'Not Modified',
        HTTP_305 = 'Use Proxy',
        HTTP_307 = 'Temporary Redirect',
        HTTP_400 = 'Bad Request',
        HTTP_401 = 'Unauthorized',
        HTTP_402 = 'Payment Required',
        HTTP_403 = 'Forbidden',
        HTTP_404 = 'Not Found',
        HTTP_405 = 'Method Not Allowed',
        HTTP_406 = 'Not Acceptable',
        HTTP_407 = 'Proxy Authentication Required',
        HTTP_408 = 'Request Timeout',
        HTTP_409 = 'Conflict',
        HTTP_410 = 'Gone',
        HTTP_411 = 'Length Required',
        HTTP_412 = 'Precondition Failed',
        HTTP_413 = 'Request Entity Too Large',
        HTTP_414 = 'Request-URI Too Long',
        HTTP_415 = 'Unsupported Media Type',
        HTTP_416 = 'Requested Range Not Satisfiable',
        HTTP_417 = 'Expectation Failed',
        HTTP_500 = 'Internal Server Error',
        HTTP_501 = 'Not Implemented',
        HTTP_502 = 'Bad Gateway',
        HTTP_503 = 'Service Unavailable',
        HTTP_504 = 'Gateway Timeout',
        HTTP_505 = 'HTTP Version Not Supported';

    protected $_url;
    protected $_destinations = array();
    protected $_controller;
    protected $_handler;
    protected $_parts;
    protected $_default;
    protected $_alias;
    protected $_current;

    public function __construct()
    {
        $this->_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        if ($pos = strpos($this->_url, '?'))
        {
            parse_str(substr($this->_url, strpos($this->_url, '?') + 1), $getVars);
            $this->_url = substr($this->_url, 0, strpos($this->_url, '?'));
            Request\Get::sArray($getVars);
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
        $this->_alias[$controller] = &$this->_destinations[$index];
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


        $this->error404();
        //$this->call($this->_destinations[$this->_default]);
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

        Registry::Callback()->invoke('beforeCreateControllerClass', array(&$prepost, &$parts, &$handler, $dest));
        $this->_controller = new $dest['controller']();
        if (method_exists($this->_controller, 'controller_' . $handler))
        {
            $reflector = new \ReflectionClass($dest['controller']);
            $q = $reflector->getMethod('controller_' . $handler)->getParameters();

            $params = $reflector->getMethod('controller_' . $handler)->getParameters();
            Registry::Callback()->invoke('beforeControllerValidation', array($reflector, &$params, &$parts));

            foreach ($params as $i => $param)
            {
                if (empty($parts[$i]) and !($canEmpty = $param->isDefaultValueAvailable()))
                {
                    return false;
                }

                Request\Get::s($param->name, !empty($parts[$i]) ? $parts[$i] : null);
                $attributes[] = Request\Get::g($param->name);
            }
            $this->_current = $dest;
            $this->_handler = $handler;
            $this->_controller->preroute($handler);
            Registry::Callback()->invoke('beforeCallControllerHandler', array(&$prepost, &$parts, &$handler, $dest));
            call_user_func_array(array($this->_controller, 'controller_' . $handler), $attributes);
            Registry::Callback()->invoke('afterRoute', array(&$prepost, &$parts, &$handler, $dest));
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

    public function redirect($url)
    {
        $url = $url[0] == '/' ? SITE . substr($url, 1) : $url;
        header('Location: ' . $url);
        exit;
    }

    public function refresh()
    {
        $url = $_SERVER['REQUEST_URI'];
        $p = strpos($url, '?');
        $url = $p ? substr($url, 0, $p) : $url;

        $this->redirect($url);
    }

    public function error($code)
    {

        $er = '\Xa\Router::HTTP_' . $code;
        //if (defined('\Xa\Router::HTTP_' . $code))
        //{
        header("HTTP/1.1 $code " . constant($er));
        header("Status: $code " . constant($er));
        exit();
        // }

        throw new Exceptions\ErrorCodeNotFound($code);
    }

    public function error404()
    {
        $this->error(404);
    }

}

?>