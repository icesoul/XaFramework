<?php

namespace Xa\Interfaces;

interface Callback
{

    public function __construct();

    public function invoke($event, array $attrs = array());


    public function moreInvoke(array $events, array $attrs = array());

    public function register($event, $callback);

    public function handlersIsExists($event);

    public function __set($event, $callback);
}