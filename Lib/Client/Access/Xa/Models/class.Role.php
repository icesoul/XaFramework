<?php

namespace Xa\Lib\Client\Access\Xa\Models;

class Role extends \ActiveRecord\Model
{

    protected static $_safe = false;
    static $has_many = array(
        array('ar', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\Ar'),
        array('at', 'class_name' => '\Xa\Lib\Client\Access\Xa\Models\At', 'through' => 'ar', 'order' => 'priority ASC'),
    );

    public function recalculate ()
    {
        $accesses = array();
        foreach ($this->at as $template)
        {
            foreach ($template->accesses as $access)
            {
                $accesses[$access->name] = $access->value;
            }
        }

        $this->access = serialize($accesses);
        $this->save();
    }

    public static function recalculateAll ()
    {
        foreach (Role::all() as $role)
        {
            $role->recalculate();
        }
    }

    public function joinTemplate (Models\At $template)
    {
        return Models\Ar::create(array('role_id' => $this->id, 'at_id' => $template->id))->is_valid();
    }

    public function clearTemplates ()
    {
        foreach ($this->ar ? : array() as $relation)
        {
            $relation->delete();
        }
    }

    public function customTemplate ()
    {
        return Ar::find_by_role_id($this->id, array("order" => "created_at ASC", "limit" => 1))->at;
    }

    public function __toString ()
    {
        return $this->title;
    }

    public static function safeFullRecalculate ()
    {
        if (static::$_safe)
        {
            return;
        }
        static::$_safe = true;
        register_shutdown_function(function()
                {
                    Role::recalculateAll();
                });
    }

}

?>