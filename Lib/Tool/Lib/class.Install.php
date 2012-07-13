<?php
namespace Xa\Lib\Tool\Lib;

class Install extends \Xa\Controller
{
    public function controller_index()
    {

    }


    public function controller_install($lib)
    {
        $l = new \Xa\Lib\Sys\Lib('\Xa\Lib\Data\Fields');
        $l->install();
    }
}