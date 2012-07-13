<?php

namespace Xa\Request;

class Post extends Base implements \Xa\Request\Interfaces\Post
{

    protected  $_data = array();
    protected  $_type = 'post';

    public  function __construct()
    {
        $this->_data = $_POST;
    }

}

?>