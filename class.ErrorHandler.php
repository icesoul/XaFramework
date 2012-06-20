<?php

namespace Xa;

class ErrorHandler
{

    protected $_view;

    public function __construct(View $view)
    {
        $this->_view = $view;
    }

    protected function handler($title, $msg=null, $class=null, $function=null, $file=null, $line=null, $backtrace=null)
    {
        Registry::Callback()->invoke('error', array(&$title, &$msg, &$class, &$function, &$file, &$line, &$backtrace));
        $view = $this->_view;
        $view->title = $title;
        $view->msg = $msg;
        $view->class = $class;
        $view->function = $function;
        $view->file = $file;
        $view->line = $line;
        $view->backtrace = $backtrace;
        echo $view->render();
        exit();
    }

    public function error($errno, $errmsg, $file, $line)
    {


        $this->handler('System error', $errmsg, null, null, $file, $line, debug_backtrace());
    }

    public function exception($exception)
    {
        $this->handler('System error (Uncaught exception)', $exception->getMessage(), get_class($exception), null, $exception->getFile(), $exception->getLine(), $exception->getTrace());
    }

}

?>
