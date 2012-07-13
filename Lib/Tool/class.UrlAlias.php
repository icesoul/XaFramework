<?php

namespace Xa\Lib\Tool;

class UrlAlias extends \ActiveRecord\Model
{

    public static $table_name = 'url_alias';

    public static function detect ()
    {
        $router = \Xa\Registry::Router();

        try
        {
            $alias = static::find($router->getPrepost());

            $router->setPrepost($alias->to);
        }
        catch (\ActiveRecord\RecordNotFound $e)
        {
            return false;
        }
    }

}

?>