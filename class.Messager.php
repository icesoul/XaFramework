<?php

namespace Xa;

use \Iterator;

/**
 *  Messages storage (Flash messages)
 */
class Messager implements Iterator
{

    const ok = 'success';
    const warning = 'error';
    const notice = 'warning';
    const nextRoute = 'send';
    const online = 'append';

    protected $_messages = array();

    public function __construct ()
    {
        //  var_dump( $_SESSION['messages']);
        $this->_messages = empty($_SESSION['messages']) ? array() : $_SESSION['messages'];
        $_SESSION['messages'] = array();
    }

    /**
     * Send custom message
     * 
     * @param string $message Message
     * @param string $level Level,uuse constants Messager::ok,Messager::warning,Messager::notice
     * @return Messager 
     */
    public function send ($message, $level = self::ok)
    {
        $_SESSION['messages'][] = array($message, $level);
        return $this;
    }

    /**
     * Send error message
     * @param string $message Message
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager 
     */
    public function error ($message, $type = self::nextRoute)
    {
        $this->$type($message, self::warning);
        return $this;
    }

    /**
     * Send notice message
     * @param string $message Message
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager 
     */
    public function notice ($message, $type = self::nextRoute)
    {
        $this->$type($message, self::notice);
        return $this;
    }

    /**
     * Send ok message
     * @param string $message Message
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager 
     */
    public function ok ($message, $type = self::nextRoute)
    {
        $this->$type($message, self::ok);
        return $this;
    }

    /**
     * Send message current page
     * @param string $message Message
     * @param string $level Level,uuse constants Messager::ok,Messager::warning,Messager::notice
     * @return Messager 
     */
    public function append ($message, $level = self::ok)
    {

        $this->_messages[] = array($message, $level);
        return $this;
    }

    /**
     * Get all messager
     * Or use foreach(Registry::Messager() as $message){}
     * @return array All messages 
     */
    public function getMessages ()
    {
        return $this->_messages ? : array();
    }

    public function exists ()
    {

        return ! empty($this->_messages);
    }

    /**
     * Parse model errors,and send (append) messages
     *
     * @param \ActiveRecord\Model $model source model
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager 
     */
    public function includeModelErrors (\ActiveRecord\Errors $errors, $type = self::nextRoute)
    {
        foreach ($errors->full_messages() as $message)
        {
            $this->$type($message, self::warning);
        }
        return $this;
    }

    // iterator 
    public function rewind ()
    {
        reset($this->_messages);
    }

    public function current ()
    {
        return current($this->_messages);
    }

    public function key ()
    {
        return key($this->_messages);
    }

    public function next ()
    {
        return next($this->_messages);
    }

    public function valid ()
    {
        $key = key($this->_messages);
        return ($key !== NULL && $key !== FALSE);
    }

}

?>