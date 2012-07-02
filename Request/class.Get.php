<?php

namespace Xa\Request;

class Get extends Base implements  \Xa\Request\Interfaces\Get
{

    protected $_data = array();
    protected $_type = 'get';

    public function __construct()
    {
        $this->_data = $_GET;
    }

}

?>
