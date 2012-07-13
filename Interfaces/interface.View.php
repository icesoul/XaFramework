<?php

namespace Xa\Interfaces;

interface View
{


    public function getTemplate();

    public function setTemplate($template);

    public function __get($var);

    public function __set($var, $value);

    public function set($var, $value);

    public function resetData(array $data);

    public function render();

    public function deleteDataVar( /* ... */);

    public function getVars();

    public function setPublicVars(array $vars);

    public function getPublicVars();

    public function includeView($file);

    public function __toString();

}

?>