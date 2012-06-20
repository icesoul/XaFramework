<?php

namespace Xa\Lib\Javascript;

class JqueryFileUpload
{

    public function __construct ()
    {
        
    }

    public static function getInitCode ()
    {
        $view = new \Xa\View();
        $view->setTemplate(__DIR__ . '/jfu/js');

        return $view;
    }

    public static function getFormCode ()
    {
        $view = new \Xa\View();
        $view->setTemplate(__DIR__ . '/jfu/form');

        return $view;
    }

}

?>