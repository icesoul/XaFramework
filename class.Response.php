<?php
namespace Xa;

class Response implements \Xa\Interfaces\Response
{

    public function __construct()
    {

    }

    public function redirect($url)
    {
        $url = $url[0] == '/' ? SITE . substr($url, 1) : $url;
        header('Location: ' . $url);
        exit;
    }

    public function refresh()
    {
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $p = strpos($url, '?');
        $url = $p ? substr($url, 0, $p) : $url;

        $this->redirect($url);
    }

    public function error($code)
    {
        $er = '\Xa\Response::HTTP_' . $code;

        header("HTTP/1.1 $code " . constant($er));
        header("Status: $code " . constant($er));
        exit();


        throw new Exceptions\ErrorCodeNotFound($code);
    }

    public function error404()
    {
        $this->error(404);
    }


    public function output($out)
    {
        echo is_a($out, '\Xa\Interfaces\View') ? $out->render() : $out;
    }


    /**
     * @static
     * @return Response
     */
    public static function create()
    {
        return IoC\Factory::create(get_called_class(), func_get_args());
    }
}