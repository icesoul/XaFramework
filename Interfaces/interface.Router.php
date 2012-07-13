<?php

namespace Xa\Interfaces;

interface Router
{


    public static function create();

    public function addDestination($prepost, $controller);

    public function setDefaultDestination($prepost, $controller);


    public function route();

    public function getByController($controller);

    public function getCurrent();

    public function getController();

    public function getHandler();

    public function getUrl();

    public function getPrepost();

    public function setPrepost($prepost);


}

?>