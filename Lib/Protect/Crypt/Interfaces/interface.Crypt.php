<?php


namespace Xa\Lib\Protect\Crypt\Interfaces;

interface Crypt
{

    public function equal($to);

    public function set($str);

    public function get();

    //  public function setLevel($lvl);

    public function __toString();
}
