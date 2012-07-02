<?php

namespace Xa\Lib\Client\Access\Xa;

class Area51 extends Access
{

    public function __construct()
    {

    }

    public function allow($action)
    {
        return false;
    }

    public function deny($action)
    {
        return true;
    }

}

?>