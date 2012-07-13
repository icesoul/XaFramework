<?php

namespace Xa\Lib\Gena;

class Controller extends \Xa\Controller
{

    public function controller_index()
    {
        $table = \Model\Product::table();
        $A = new Analyser($table);
    }
}
