<?php

namespace Xa\Request\Interfaces;

interface File extends Request
{
    /**
     * @abstract
     *
     * @param $index
     *
     * @return \Xa\Request\Filer
     */
    public function g($index);

}

?>