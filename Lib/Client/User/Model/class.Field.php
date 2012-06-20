<?php
namespace Xa\Lib\Client\User\Model;

class Field extends \ActiveRecord\Model
{
    static $has_many = array(
        array(
            'uf',
            'class_name' => '\Xa\Lib\Client\User\Model\Uf'
        ),
        array(
            'users',
            'class_name' => '\Xa\Lib\Client\User\Model\User',
            'through' => 'uf'
        )
    );

    public function __toString()
    {

        return isset($this->value) && !is_null($this->value) ? $this->value : $this->default;
    }
}

?>