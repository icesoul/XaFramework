<?php

namespace Xa\Lib\Client\Access\Xa;

class Access
{

    protected static $_outside = false;

    const allow = 'allow';
    const deny = 'deny';
    const guestRole = 1;

    /**
     *
     * @var Model
     */
    protected $_role;
    protected $_roleId;
    protected $_list;

    public function __construct (Models\Role $role)
    {
        if (static::$_outside !== true)
        {
            trigger_error('Use Access::make($roleId)', E_USER_WARNING);
        }

        $this->_role = $role;

        $this->_roleId = $role->id;
        $this->_list = unserialize($role->access);
    }

    public function reload ()
    {
        $this->_list = unserilize($role->access);
    }

    public function autoMode ()
    {
        $access = $this;
        \Xa\Registry::Callback()->register('beforeCreateControllerClass', function($prepost, $parts, $handler, $dest) use ($access)
                {
                    $accessName = strtolower(str_replace('\\', '_', substr($dest['controller'], 1)) . '_' . $handler);

                    if ( ! $access->allow($accessName))
                    {
                        if (\Xa\Registry::Callback()->handlersIsExists('AccessAutoDenied'))
                        {
                            \Xa\Registry::Callback()->invoke('AccessAutoDenied');
                        }
                        else
                        {
                            \Xa\Registry::Router()->error(403);
                        }
                    }
                });
    }

    public function autoFill ($roleId)
    {
        $role = Models\Role::find($roleId);
        $template = $role->customTemplate();
        $access = unserialize($role->access);
        $thisObj = $this;
        \Xa\Registry::Callback()->register('beforeCreateControllerClass', function($prepost, $parts, $handler, $dest) use (&$access, $role, $thisObj)
                {
                    $accessName = strtolower(str_replace('\\', '_', substr($dest['controller'], 1)) . '_' . $handler);
                    if ( ! array_key_exists($accessName, $access))
                    {

                        try
                        {
                            $model = Models\Access::find($accessName);
                        }
                        catch (\ActiveRecord\RecordNotFound $e)
                        {
                            $model = Models\Access::create(array("name" => $accessName, "title" => $accessName));
                        }

                        $template = $role->customTemplate();
                        $s = Models\Atr::create(array('access_id' => $model->id, 'at_id' => $template->id, 'value' => 'allow'));
                        $thisObj->reload();
                    }
                });

        //  
    }

    /**
     * Make access by role id
     * 
     * @param int $userRoleId role id
     * @return \Xa\Lib\Client\Access\Xa\Area51|\Xa\Lib\Client\Access 
     */
    public static function make ($userRoleId)
    {
        try
        {
            $role = Models\Role::find($userRoleId);
        }
        catch (\ActiveRecord\RecordNotFound $e)
        {
            try
            {
                $role = Models\Role::find(static::guestRole);
            }
            catch (\ActiveRecord\RecordNotFound $e)
            {
                return new Area51();
            }
        }
        $class = get_called_class();
        static::$_outside = true;

        return new $class($role);
    }

    public function allow ($action)
    {
        return isset($this->_list[$action]) && $this->_list[$action] == static::allow;
    }

    public function deny ($action)
    {
        return isset($this->_list[$action]) && $this->_list[$action] == static::deny;
    }

    public function allowOr403 ($action)
    {
        if ( ! $this->allow($action))
        {
            \Xa\Router::error(403);
        }
    }

}

?>
