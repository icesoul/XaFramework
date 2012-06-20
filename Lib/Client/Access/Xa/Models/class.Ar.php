<?php

namespace Xa\Lib\Client\Access\Xa\Models;

class Ar extends \ActiveRecord\Model
{

    static $belongs_to = array(
        array('at', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\At'),
        array('role', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Role'),
    );
    static $table_name = 'access_relations';
    static $after_save = array('callRecalculate');

    public function callRecalculate ()
    {
        Role::safeFullRecalculate();
    }

    public function __toString ()
    {
        return (string) $this->at . ' - ' . (string) $this->role;
    }

}

?>