
<?php

$bConfig = new \Xa\Config(array(
            'login' => 'root',
            'password' => 'qwerty',
            'host' => 'localhost',
            'encoding' => 'utf8',
            'models' => 'Models',
            'db' => 'Xa',
        ));
?>
<?php

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
?>

<?php

$bConfig = new \Xa\Config(array(
            'logFile' => '/Users/Anton/Sites/xareloadd_clone/Xa/Logs/messages.php',
            'logViewFile' => '/Users/Anton/Sites/xareloadd_clone/Xa/View/error.php',
        ));
?>
<?php

$logger = Xa\Registry::set('Logger', new Xa\Logger());
$logger->setLogFile($bConfig->logFile);
$logger->setLogViewFile($bConfig->logViewFile);
set_error_handler(array($logger, 'systemErrorHandler'));
set_exception_handler(array($logger, 'systemExceptionHandler'));
?>

