<?php

namespace Xa\Lib\Sys;

class ErrorScope
{
    protected $_log;

    public function __construct()
    {
        $this->_log = \Xa\Registry::Log();
    }

    public function handler($title, $msg = null, $class = null, $function = null, $file = null, $line = null, $backtrace = null)
    {
        $text = "File: $file\nLine $line\nClass $class\nFunction $function\n";
        $text .= "\tBacktrace:\n";
        foreach ($backtrace as $trace)
        {
            $trace = array_merge(array(
                                      'file' => null,
                                      'line' => null,
                                      'class' => null,
                                      'function' => null
                                 ), $trace);
            $text .= "\t\tFile: {$trace['file']}\n\t\tLine: {$trace['line']}\n\t\tClass: {$trace['class']}\n\t\tFunction: {$trace['function']}";
        }

        $this->_log->log($title, $text);
    }

    public function scopeXaErrors()
    {
        \Xa\Registry::Callback()->register('error', array(
                                                         $this,
                                                         'handler'
                                                    ));
    }
}

?>