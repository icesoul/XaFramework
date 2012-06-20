<?php

namespace Xa\Lib\Client\Access\Xa\Models;

class Access extends \ActiveRecord\Model
{

    static $table_name = 'accesses';
    static $primary_key = 'name';
    static $validates_presence_of = array(
        array('name')
    );
    static $validates_size_of = array(
        array('name', 'within' => array(2, 40)),
        array('title', 'within' => array(2, 100), 'allow_blank' => true),
    );
    static $has_many = array(
        array('atr', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Atr'),
        array('at', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\At', 'through' => 'atr')
    );
    protected $_name;
    static $after_save = array('callRecalculate');

    public function callRecalculate ()
    {
        Role::safeFullRecalculate();
    }

    public function __toString ()
    {
        return $this->title ? : $this->name;
    }

}

?>