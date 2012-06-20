<?php

namespace Xa\Lib\Protect;

class Token
{
    protected $_id;
    protected $_token;
    protected $_request;

    public function __construct ($tokenId, $validation = false)
    {
        $this->_id = $tokenId;
        $this->_token = $_SESSION[$tokenId] = isset($_SESSION[$tokenId]) ? $_SESSION[$tokenId] : md5(randLetters(50, 100));
        $this->_request = (string) \Xa\Request\Post::g('token_' . $tokenId)->canEmpty()->safe();

        if ($validation)
        {
            $this->aggresive();
        }
        //$this->_token = 
    }

    public function aggresive ()
    {
        if ($this->_request != $this->_token)
        {
            \Xa\Registry::Router()->error(403);
        }
        unset($_SESSION[$this->_id]);
    }

    public function __toString ()
    {
        return "<input type='hidden' name='token_$this->_id' value='$this->_token'/>\n";
    }

}

?>