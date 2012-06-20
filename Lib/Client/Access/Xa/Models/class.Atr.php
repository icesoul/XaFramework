<?php

namespace Xa\Lib\Client\Access\Xa\Models;

/**
 * Отношение правил доступа к шаблонам
 */
class Atr extends \ActiveRecord\Model
{

    static $table_name = 'access_templates_relations';
    static $belongs_to = array(
        array('access', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Access'),
        array('at', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\At'),
    );
    static $validates_uniqueness_of = array(
       // array('access_id', 'at_id')
    );
    static $after_save = array('callRecalculate');

    public function callRecalculate ()
    {
        Role::safeFullRecalculate();
    }

    public function updateAccess ()
    {
        foreach ($this->at->ar as $relation)
        {
            Role::updateAccess($relation->role_id);
        }
    }

    public function __toString ()
    {
        return ($this->access . '-' . $this->at . ' (' . $this->value . ')');
    }

}

?>