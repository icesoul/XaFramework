<?php

namespace Xa\Request\Interfaces;

interface  Cookie extends Request
{

    public function send($index, $value, $expire = 0, $path = '/', $domain = false, $secure = false, $http = false);
}

?>