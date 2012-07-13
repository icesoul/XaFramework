<?php

namespace Xa\Interfaces;

interface Cache
{
    const AsArray = 1;
    const AsString = 0;
    const AsStd = 2;

    public function  __construct($dest, $key);

    public function set($var, $value, $lifetime, array $tags = array());

    public function get($var);

    public function clear($var);

    public function clearByTags(array $tags);

    public function clearAll();
}

?>