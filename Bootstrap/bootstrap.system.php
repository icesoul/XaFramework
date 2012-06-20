<?php

session_start();
mb_internal_encoding($bConfig->charset);
header('Content-Type: text/html; charset=' . $bConfig->charset);
date_default_timezone_set($bConfig->timezone);
setlocale(LC_ALL, 'ru_RU');


$domain = $_SERVER['SERVER_NAME'];
$xaPath = \Xa\Core::getRoot();
$root = $_SERVER['DOCUMENT_ROOT'];
$subfolder = substr($xaPath, strpos($xaPath, $root) + strlen($root));


define('Xa\SITE', 'http://' . $domain . '/' . $subfolder . '/');
define('Xa\CSITE', 'http://' . $domain . '/' . $subfolder);
define('Xa\SITEWWW', 'http://www.' . $domain . '/' . $subfolder . '/');




?>