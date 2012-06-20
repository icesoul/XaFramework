<?php

namespace Xa\Request;

class EmptySpeller
{

    public function isEmpty ()
    {
        return true;
    }

    public function __call ($a, $b)
    {
        return $this;
    }

    public function __toString ()
    {
        return '';
    }

}

?>
