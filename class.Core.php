<?php

namespace Xa;

use Closure;

class Core
{


    public static $ignoreLayout = false;
    public static $json = false;
    public static $ignoreView = false;
    public static $timestamp;

    private function __construct()
    {

    }

    public static function prestart()
    {
        require __DIR__ . '/common.php';
        static::initSetConstants();
        static::initAutoload();
        require __DIR__ . '/class.Registry.php';
        require __DIR__ . '/class.Config.php';
        require __DIR__ . '/class.Callback.php';
        require __DIR__ . '/class.Cache.php';
        require __DIR__ . '/Cache/class.Xa.php';

        \Xa\Registry::set('Log', new \Xa\Logs());
        Registry::set('Cache', new Cache\Xa(new Config(array('destination' => __DIR__ . '/temp/'))));
        Registry::set('Callback', $cb = new Callback);


        // Registry::set('Cache', new Cache\Xa(new Config(array('destination' => __DIR__ . '/temp/'))));
    }

    public static function init(Config $config)
    {
        Registry::Callback()->invoke('prestart');

        Registry::set('Config', $config);
        $bootstrap = array(__DIR__ . '/Bootstrap/');
        if (isset($config->bootstraps))
        {
            $bootstrap = array_merge($bootstrap, $config->bootstraps);
        }

        $md5 = md5(serialize($config->all()));

        /* if (!file_exists(__DIR__ . '/Bootstrap/' . $md5))
          {
          static::loadFromConfiguration($bootstrap);
          goto next;
          }


          $dateCreatedLastCompile = filemtime(__DIR__ . '/Bootstrap/' . $md5); */
        foreach ($bootstrap as $path)
        {
            //  if (filemtime($path) > $dateCreatedLastCompile)
            //{
            @unlink(__DIR__ . '/Bootstrap/' . $md5);
            static::loadFromConfiguration($bootstrap);
            goto next;
            break;
            // }
        }


        require __DIR__ . '/Bootstrap/' . $md5;


        next:
        static::initSupportClasses();
        static::initParseRequestData();
        static::$timestamp = new \DateTime('@' . time());
        Registry::Callback()->invoke('postInit');
    }

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

    protected static function loadFromConfiguration($bootstrap)
    {


        $config = Registry::Config();

        $list = $config->priority;
        foreach ($bootstrap as $dir)
        {

            $files = glob($dir . 'bootstrap.*.php');


            usort($files, function($a, $b) use ($list)
            {
                $a = substr(basename($a, '.php'), 10);
                $b = substr(basename($b, '.php'), 10);
                $aExists = array_key_exists($a, $list);
                $bExists = array_key_exists($b, $list);

                if ($aExists && !$bExists)
                {
                    return 1;
                }
                elseif ($bExists && !$aExists)
                {
                    return 0;
                }
                else
                {
                    return $list[$a] > $list[$b] ? 1 : 0;
                }
            });


            foreach ($files as $file)
            {
                $name = substr(basename($file, '.php'), 10);

                $bConfig = isset($config->{$name}) ? new Config($config->$name) : new Config();


                include($file);

                unset($bConfig);
            }
        }

        static::compilationBootstrap($bootstrap);
    }

    public static function getRoot()
    {
        return dirname(__DIR__);
    }

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

}

?>