<?php

namespace Xa\Lib\Protect;

class Token
{
    protected $_id;
    protected $_token;
    protected $_request;

    public $Response;


    public function __construct(\Xa\Interfaces\Response $Response, \Xa\Request\Interfaces\Get $Get, \Xa\Request\Interfaces\Post $Post, $tokenId, $validation = false)
    {

        $this->_id = $tokenId;
        $this->Response = $Response;
        $this->_request = $Get->exists('token_' . $tokenId) ? $Get->g('token_' . $tokenId) : $Post->g('token_' . $tokenId);
        $this->_request = (string)$this->_request->canEmpty()->safe();
        $stored = isset($_SESSION[$tokenId]) ? $_SESSION[$tokenId] : null;
        if (empty($this->_request))
        {
            $this->_token = $_SESSION[$tokenId] = md5(randLetters(50, 100));
        }
        elseif ($stored)
        {

            $this->_token = $_SESSION[$tokenId] = $stored;
        }
        else
        {

            $this->aggresive();
        }


        if ($validation)
        {
            $this->aggresive();
        }
    }

    public function aggresive()
    {

        if ($this->_request != $this->_token)
        {
            $this->Response->error(403);
        }
        unset($_SESSION[$this->_id]);
    }

    public function get()
    {
        return $this->_token;
    }

    public function __toString()
    {
        return "<input type='hidden' name='token_$this->_id' value='$this->_token'/>\n";
    }


    public function asParam()
    {
        return '?token_' . $this->_id . '=' . $this->_token;
    }

    /**
     * @static
     * @return Token
     */
    public static function create()
    {
        return \Xa\IoC\Factory::create(get_called_class(), func_get_args());
    }
}

?>