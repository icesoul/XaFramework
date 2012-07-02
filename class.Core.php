<?php

namespace Xa;

use Closure;


class Core
{
    public static $ignoreLayout = false;
    public static $json = false;
    public static $ignoreView = false;
    public static $timestamp;

    public function __construct()
    {
        define('Xa', true);
        define('Xa\DR', DIRECTORY_SEPARATOR);
        define('Xa\AP', str_replace("\\", '/', getcwd()) . '/');
        define('Xa\SITE', 'http://192.168.1.43/xadevel/');
        define('Xa\CSITE', 'http://192.168.1.43/xadevel/');

        require __DIR__ . '/common.php';

        spl_autoload_register('\xaAutoload');
        spl_autoload_register('\xaExceptionAutoload');
        $ioc = IoC\Container::getInstance();
        $ioc->register(new Request\Get());
        $ioc->register(new Request\Post());
        $ioc->register(new Request\Cookie());
        $ioc->register($r = Router::create(), 'Router');
        $ioc->register(Response::create());
        $ioc->register(new Callback());


    }


    public function init()
    {

        $bootstrap = __DIR__ . '/../Cfg/';
        foreach (new \DirectoryIterator($bootstrap) as $cfgFile)
        {
            $filename = $cfgFile->getPath() . '/' . $cfgFile->getFilename();
            $cfgFor = basename($filename, '.php');
            if (is_file($filename) && file_exists($bFile = __DIR__ . '/Bootstrap/bootstrap.' . $cfgFor . '.php'))
            {
                $bConfig = include($filename);
                include($bFile);
            }
        }


    }

    /*
        public static function setMainLayout($path)
        {
            if (!Registry::registered('Layout'))
            {
                Registry::set('Layout', new Layout());
            }
            Registry::Layout()->setTemplate($path);
        }

        public static function fire($layout)
        {
            Registry::Callback()->invoke('preFire');
            Registry::Router()->route();
            Registry::Callback()->invoke('postFire');

            static::stop();
        }

        public static function stop()
        {
            $layout = Registry::Layout();
            if (static::$ignoreLayout or Request\Get::exists('ignoreLayout'))
            {
                echo $layout->getContent();
            }
            elseif (static::$json or Request\Get::exists('json'))
            {

                echo json_encode($layout->getContentView()->getPublicVars());
            }
            else
            {
                echo $layout->render();
            }
        }

        protected static function initSupportClasses()
        {
            Registry::set('Router', new Router);
            Registry::set('Messager', new Messager());
        }

        protected static function initSetConstants()
        {
            define('Password', 'qwerty');
            define('Xa', true);
            define('Xa\DR', DIRECTORY_SEPARATOR);
            define('Xa\SystemId', '1234567890');
            define('Xa\AP', str_replace("\\", '/', getcwd()) . '/');
        }

        protected static function initAutoload()
        {
            spl_autoload_register('\xaAutoload');

            spl_autoload_register('\xaExceptionAutoload');
        }

        protected static function initParseRequestData()
        {
            Request\Get::build();
            Request\Post::build();
            Request\Cookie::build();
            Request\File::build();
        }



        public static function getRoot()
        {
            return dirname(__DIR__);
        }
    /*
        protected static function compilationBootstrap(array $bootstrap)
        {
            $config = Registry::Config();
            $code = null;

            foreach ($bootstrap as $dir)
            {
                foreach (glob($dir . 'bootstrap.*.php') as $file)
                {
                    $name = substr(basename($file, '.php'), 10);

                    $bConfig = isset($config->{$name}) ? new Config($config->$name) : new Config();

                    $code .= "<?php \n\$bConfig=new \\Xa\\Config(" . var_export($bConfig->all(), true) . ");\n ?>";
                    $code .= file_get_contents($file);
                }
            }

            $path = __DIR__ . '/Bootstrap/' . md5(serialize($config->all()));
            file_put_contents($path, $code);
            chmod($path, 0777);
        }
    */
}

?>