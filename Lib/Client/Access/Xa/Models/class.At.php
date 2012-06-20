<?php

namespace Xa\Lib\Client\Access\Xa\Models;

/**
 * Модель реализующая шаблоны доступа
 *
 */
class At extends \ActiveRecord\Model
{

    static $validates_numericality_of = array(
        array('priority', 'only_integer' => true)
    );
    static $validates_presence_of = array(
        array('title')
    );
    static $validates_size_of = array(
        array('title', 'within' => array(2, 100)),
        array('priority', 'within' => array(1, 5), 'allow_blank' => true),
    );
    static $table_name = 'access_templates';
    static $has_many = array(
        array('ar', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Ar'),
        array('roles', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Role', 'through' => 'ar'),
        array('atr', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Atr'),
        array('accesses', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Access', 'through' => 'atr', 'select' => 'name,title,value')
    );
    static $after_save = array('callRecalculate');

    public function callRecalculate ()
    {
        Role::safeFullRecalculate();
    }

    public function updateAccess ()
    {
        foreach ($this->ar as $relation)
        {
            Role::updateAccess($relation->role_id);
        }
    }

    public function destroyRelations ()
    {
        $roles = array();
        foreach ($this->ar as $relation)
        {
            $roles[] = $relation->role_id;
        }
        Ar::table()->delete(array('at_id' => $this->id));
        Role::updateAccessByRoles($roles);
    }

    /**
     * Добавление нового правила доступа в шаблон
     * 
     * @param Access $access 
     */
    public function joinAccess (Access $access)
    {
        $m = Atr::create(array('access_id' => $access->name, 'at_id' => $this->id));
        return $m->is_valid();
    }

    public function clearAccesses ()
    {
        foreach ($this->atr ? : array() as $relation)
        {
            $relation->delete();
        }
    }

    public function __toString ()
    {
        return $this->title;
    }

}

?>