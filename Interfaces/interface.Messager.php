<?php

namespace Xa\Interfaces;

interface Messager
{

    const ok = 'success';
    const warning = 'error';
    const notice = 'warning';
    const nextRoute = 'send';
    const online = 'append';

    /**
     * Send custom message
     *
     * @param string $message Message
     * @param string $level Level,uuse constants Messager::ok,Messager::warning,Messager::notice
     * @return Messager
     */
    public function send($message, $level = self::ok);

    /**
     * Send error message
     * @param string $message Message
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager
     */
    public function error($message, $type = self::nextRoute);

    /**
     * Send notice message
     * @param string $message Message
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager
     */
    public function notice($message, $type = self::nextRoute);

    /**
     * Send ok message
     * @param string $message Message
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager
     */
    public function ok($message, $type = self::nextRoute);

    /**
     * Send message current page
     * @param string $message Message
     * @param string $level Level,uuse constants Messager::ok,Messager::warning,Messager::notice
     * @return Messager
     */
    public function append($message, $level = self::ok);

    /**
     * Get all messager
     * Or use foreach(Registry::Messager() as $message){}
     * @return array All messages
     */
    public function getMessages();

    public function exists();

    /**
     * Parse model errors,and send (append) messages
     *
     * @param \ActiveRecord\Model $model source model
     * @param type $type Online or next route? Use Messager::nextRoute,Messager::online
     * @return Messager
     */
    public function includeModelErrors(\ActiveRecord\Errors $errors, $type = self::nextRoute);

    // iterator
    public function rewind();

    public function current();

    public function key();

    public function next();

    public function valid();

}

?>