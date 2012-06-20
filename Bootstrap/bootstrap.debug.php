<?php

/* $logger = Xa\Registry::set('Logger', new Xa\Logger());
  $logger->setLogFile($bConfig->logFile);
  $logger->setLogViewFile($bConfig->logViewFile);
  set_error_handler(array($logger, 'systemErrorHandler'));
  set_exception_handler(array($logger, 'systemExceptionHandler')); */

$handler = new Xa\ErrorHandler(new \Xa\View(Xa\AP . 'Xa/View/error'));
\Xa\Registry::set('Debug', $handler);
set_error_handler(array($handler, 'error'));
set_exception_handler(array($handler, 'exception'));

?>
