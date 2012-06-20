<?php

namespace Xa\Lib\Client\User\Model;

class User extends \ActiveRecord\Model
{
    static $has_many = array(
        array(
            'uf',
            'class_name' => '\Xa\Lib\Client\User\Model\Uf'
        ),
        array(
            'fields',
            'class_name' => '\Xa\Lib\Client\User\Model\Field',
            'through' => 'uf',
            'select' => 'fields.*,value'
        )
    );
    static $before_save = array('checkVname');
    static $validates_presence_of = array(
        array('login'),
        array('vname'),
        array('email'),
        array('password'),
        array('role_id'),
    );
    static $validates_numericality_of = array(
        array('role_id')
    );
    static $validates_size_of = array(
        array(
            'login',
            'within' => array(
                3,
                40
            )
        ),
        array(
            'vname',
            'within' => array(
                3,
                35
            )
        ),
        array(
            'email',
            'within' => array(
                5,
                50
            )
        ),
        array(
            'password',
            'within' => array(
                6,
                40
            )
        ),
    );
    static $validates_format_of = array(
        array(
            'email',
            'with' => '/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/'
        ),
    );
    static $validates_uniqueness_of = array(
        array('login'),
        array('vname'),
        array('email')
    );

    public function checkVname()
    {
        if (isset($this->login))
        {
            $this->login = str_replace(' ', '', $this->login);
            $this->vname = $this->vname ? : $this->login;
        }
    }

    public function set_password($pass)
    {
        if (!empty($pass))
        {
            $this->assign_attribute('password', Xa\Lib\Client\Authorization\Sql\Auth::cryptPwd($pass));
        }
    }


    //FIXME remove suppressedstorage
    public function fields()
    {
        $this->assign_attribute('fields', new \Xa\Helper\SuppressedStorage(modelsToAssociativeArray($this->fields)));

        return $this;
    }
}

?>
