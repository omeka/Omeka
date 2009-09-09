<?php
require_once 'constants.php';

/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/application.ini'
);


$application->getBootstrap()->bootstrap(array('FrontController', 'Layout'));
if (APPLICATION_ENV !== 'testing') {
    $application->run();
}