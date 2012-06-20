<?php

namespace Xa\Lib\Client\User\Model;

class Uf extends \ActiveRecord\Model
{
    static $table_name = 'fields_to_users';

    static $belongs_to = array(
        array(
            'field',
            'class_name' => '\Xa\Lib\Client\User\Model\Field',
            'select' => '*,uf.value as value'
        ),
        array(
            'user',
            'class_name' => '\Xa\Lib\Client\User\Model\User'
        )
    );


    public static function safeCreate(User $user, Field $field)
    {
        parent::create(array(
                            'user_id' => $user->id,
                            'field_id' => $field->id
                       ));
    }
}

?>