<?php

namespace Xa\Lib\Client\Authorization\Sql;

use Xa\Request\Cookie;

class Auth implements \Xa\Lib\Client\Interfaces\Auth
{

    const authByData = 1;
    const authBySid = 2;
    const ipField = 'ip';
    const sessionField = 'sid_id';
    const onlineField = 'online';
    const lastLoginField = 'last_login';
    const lastActivityField = 'latest_activity';
    const hashField = 'hash';

    protected $_fields;
    protected $_model;
    protected $_authData = array();
    protected $_auth;
    protected $_user;
    protected $_ip;


    public $User;
    public $Crypt;

    protected $class;

    public function __construct(\Xa\Lib\Protect\Crypt\Interfaces\Crypt $Crypt)
    {
        $this->Crypt = $Crypt;
        $this->class = \Xa\IoC\Container::getInstance()->getAssoc('Xa\Lib\Client\Interfaces\User')->getClassName();
        if (!empty($_SESSION['auth']))
        {
            $this->renew();
        }

    }


    public function renew()
    {
        $class = $this->class;
        if (!isset($_SESSION['ip']) || empty($_SESSION['uid']) || $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] || $_COOKIE['hash'] != $_SESSION['hash'])
        {
            echo 'wrong';
            $this->quit();
            return;
        }
        $user = $class::check($_SESSION['uid']);


        if ($user->getAuthKey() != $_SESSION['hash'])
        {
            $user->setAuthKey(null);
            $user->store();
            $this->quit();
        }
        else
        {
            $this->User = $user;
            $this->_auth = true;
        }


    }

    public function authorize($uid, $password)
    {

        $IoC = \Xa\IoC\Container::getInstance();

        $class = $this->class;
        $password = (string)$this->Crypt->set($password);

        $status = $class::userExists($uid, $password);


        if ($status)
        {

            $this->User = $class::login($uid);
            $this->_auth = $_SESSION['auth'] = true;

            setcookie('hash', $hash = (string)$this->Crypt->set(randLetters(20)), time() + 86400, \Xa\SubFolder, \Xa\DOMAIN);

            $_SESSION['uid'] = $uid;
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['hash'] = $hash;
            $_SESSION['auth'] = true;
            $this->_auth = true;
            $this->User->setAuthKey($hash);
            $this->User->store();

        }


        /* \Xa\Registry::Callback()->invoke('beforeSqlAuthorization', $data);
       if ($diff = array_diff($authFields = array_keys($this->_authData), array_keys($data)))
       {
           throw new Exceptions\NotAllData(implode(',', $diff));
       }

       foreach ($this->_authData as $field => $type)
       {
           if ($type !== true)
           {
               $data[$field] = call_user_func($type, $data[$field]);
           }
       }

       $model = $this->_model;
       $query = 'find_by_' . implode('_and_', $authFields);

       $this->_user = $model::__callStatic($query, array_values($data));
       if (!$this->_user)
       {
           \Xa\Registry::Callback()->invoke('sqlAuthorizationFailed', array($data));
           throw new Exceptions\UserNotFound();
       }

       $this->update();
       \Xa\Registry::Callback()->invoke('sqlAuthorizationSuccess', array($this->_user));*/
    }

    public function quit()
    {

        setcookie('auth', null, null, $_SERVER['SERVER_NAME']);
        $_SESSION['uid'] = null;
        $_SESSION['auth'] = $this->_auth = false;
    }

    public function isAuth()
    {
        return $this->_auth;
    }

    /*
        public function authorizeBySid()
        {
            $model = $this->_model;
            if (!empty($_SESSION['auth']))
            {
                $user = $model::__callStatic('find_by_' . static::sessionField . '_and_' . static::ipField, array(session_id(), ip2int($this->_ip)));
                if ($user)
                {
                    $this->_user = $user;
                    $this->update();
                }
                else
                {
                    $this->clear();
                }
            }
        }
    */

    /*
   public static function cryptPwd($pwd)
   {
       return md5(md5($pwd));
   }

   public function getUser()
   {
       return $this->_user;
   }

   protected function update()
   {
       if (!$this->_user)
       {
           $this->clear();
           return false;
       }
       $this->_auth = true;
       $this->_user->{static::onlineField} = true;
       $this->_user->{static::sessionField} = session_id();
       $this->_user->{static::lastLoginField} = $_SERVER['REQUEST_TIME'];
       $this->_user->{static::lastActivityField} = $_SERVER['REQUEST_TIME'];
       $this->_user->{static::hashField} = $this->_user->{static::hashField} ? : md5(uniqid());
       $this->_user->{static::ipField} = ip2int($_SERVER['REMOTE_ADDR']);
       $_SESSION['user'] = serialize($this->_user);
       $_SESSION['auth'] = true;
       $this->_user->save();
   }

   protected function clear()
   {
       $this->_auth = false;
       if ($this->_user)
       {
           $this->_user->{static::onlineField} = false;
           $this->_user->{static::sessionField} = null;
           $this->_user->{static::lastActivityField} = $_SERVER['REQUEST_TIME'];
           $this->_user->save();
       }
       $_SESSION['user'] = serialize(array());
       $_SESSION['auth'] = false;
   }

   /**
    * @static
    * @return Auth
    */
    public static function create()
    {
        return \Xa\IoC\Factory::create(get_called_class(), func_get_args());
    }

}

?>
