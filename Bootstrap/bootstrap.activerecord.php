<?php

if (!$bConfig->disable)
{
    require __DIR__ . '/../ActiveRecord/Singleton.php';
    require __DIR__ . '/../ActiveRecord/Config.php';
    require __DIR__ . '/../ActiveRecord/Utils.php';
    require __DIR__ . '/../ActiveRecord/DateTime.php';
    require __DIR__ . '/../ActiveRecord/Model.php';
    require __DIR__ . '/../ActiveRecord/Table.php';
    require __DIR__ . '/../ActiveRecord/ConnectionManager.php';
    require __DIR__ . '/../ActiveRecord/Connection.php';
    require __DIR__ . '/../ActiveRecord/SQLBuilder.php';
    require __DIR__ . '/../ActiveRecord/Reflections.php';
    require __DIR__ . '/../ActiveRecord/Inflector.php';
    require __DIR__ . '/../ActiveRecord/CallBack.php';
    require __DIR__ . '/../ActiveRecord/Exceptions.php';
    require __DIR__ . '/../ActiveRecord/Cache.php';
    
    
    \ActiveRecord\Config::initialize(function($cfg) use ($bConfig)
            {
                if (isset($bConfig->configure))
                {
                    foreach ($bConfig->configure as $cb)
                    {
                        call_user_func($cb, $cfg);
                    }
                }
                $cfg->set_model_directory($bConfig->models);
                $cfg->set_connections(array('development' => 'mysql://' . $bConfig->login . ':' . $bConfig->password . '@' . $bConfig->host . '/' . $bConfig->db));
            });



 
    \ActiveRecord\ConnectionManager::get_connection()->query("SET NAMES '{$bConfig->encoding}';");
}
?>