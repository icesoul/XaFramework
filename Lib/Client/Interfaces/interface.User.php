<?php

namespace Xa\Lib\Client\Interfaces;

interface User
{
    public function setAuthKey($key);

    public function getAuthKey();

    public static function check($uid);

    public static function userExists($uid, $password);

    public static function login($uid);

    public function getPassword();

    public function getUID();

    public function getRoleId();


    public function setRoleId($id);

}