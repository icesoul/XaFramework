<?php
namespace Xa\Lib\Data\Fields;

function install()
{
    return array(
        'queries' => array(
            "CREATE TABLE `fields` (
        `id` varchar(11) NOT NULL,
        `type` enum('text','longtext','select','checkbox','radio') NOT NULL DEFAULT 'text',
        `default` varchar(255) DEFAULT '',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        )
    );
}

function uninstall()
{
    return array(
        'queries' => array(
            'DROP TABLE IF EXISTS `fields`;'
        )
    );
}

function installed()
{
    return \ActiveRecord\ConnectionManager::get_connection()->query('show tables like "fields"')->fetch() !== false;

}

?>
