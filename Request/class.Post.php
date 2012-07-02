<?php

namespace Xa\Request;

class Post extends Base
{

    protected  $_data = array();
    protected  $_type = 'post';

    public  function __construct()
    {
        $this->_data = $_POST;
    }

}

?>