<?php

namespace Xa;

/**
 * При указании не обязательных параметров в методе-котроллере рефлекшн работает только если опциональные параметры стоят в конце.
 *
 *
 */
abstract class Controller
{

    const autoloadView = true;

    static $_alias = false;
    static $_beforeUrl;

    /**
     * 
     * @var \Xa\View 
     */
    protected $_view;
    protected $_handler;
    protected $_called;

    public function preroute ($handler)
    {
        $this->_handler = $handler;
        $this->_called = get_called_class();
        if (static::autoloadView)
        {
            $this->_view = new View();
            $this->_view->setTemplate(dirname(Registry::Layout()->getTemplate()) . (static::$_alias !== false ? '/' . static::$_alias : namespace2RDir(get_called_class()) . '/' . getClass(get_called_class()) . '/') . $handler);
            Registry::Layout()->setContent($this->_view);
        }
    }

    public function stop ($error = null)
    {

        Registry::Callback()->moreInvoke(array('controllerStopped', get_called_class() . '_Stopped'), array($error));
        Registry::Layout()->clearContent();
        echo Registry::Layout()->error = $this->_view->error = $error;

        \Xa\Core::stop();
        exit();
    }

    public static function __callStatic ($func, array $args = array())
    {
        $namespace = get_called_class();
        $query = null;
        $args = $args ? $args[0] : array();
        $args['~'] = isset($argc['~']) ? : null;
        if ($func[0] == 'j')
        {
            $func = substr($func, 1);
            $args['~'] = isset($argc['~']) ? $args['~'] . '&json=1' : '?json=1';
        }
        $controller = strtolower(substr($func, 3));
        $reflector = new \ReflectionClass($namespace);
        $params = $reflector->getMethod('controller_' . $controller)->getParameters();





        foreach ($params as $name => $param)
        {
            $name = $param->name;
            $canEmpty = $param->isDefaultValueAvailable();
            $isEmpty = empty($args[$name]);


            if ($isEmpty and ! $canEmpty)
            {
                return null;
                //throw new Exceptions\ControllerParamError('Param ' . $param->name . ' cant be empty');
            }
            elseif ( ! $isEmpty)
            {
                $query .= $args[$name] . '/';
            }
            else
            {
                $query.= '/';
            }
        }
        ;
        if ($ctrlData = Registry::Router()->getByController('\\' . $namespace))
        {
            $prepost = $ctrlData['prepost'];
        }

        return CSITE . ($prepost == '/' ? null : $prepost) . '/' . $controller . '/' . $query . $args['~'];
    }

    public function getHandler ()
    {
        return $this->_handler;
    }

}

?>
