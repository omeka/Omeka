<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package Omeka\Install
 */

require_once 'constants.php';
require_once LIB_DIR . '/globals.php';

/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/application.ini'
);

if (APPLICATION_ENV !== 'testing') {
    $dbResource = new Omeka_Application_Resource_Db;
    $dbResource->setinipath(BASE_DIR . '/db.ini');
    $application->getBootstrap()->registerPluginResource($dbResource);   
}

// Explicitly load the Zend FrontController resource, not the Omeka modification.
// Because the Omeka FrontController resource is too heavily coupled for use.
$application->getBootstrap()->registerPluginResource('Zend_Application_Resource_FrontController', 
    array(
        'controllerDirectory' => APPLICATION_PATH . '/controllers',
        'throwExceptions' => true));
// This line is a workaround for what seems like a bug in Zend_Application.
// It loads the plugin resources, which need to be loaded before the bootstrap() 
// call so as to prevent loading the wrong resource (e.g. 
// Omeka_Application_Resource_Frontcontroller instead of 
// Zend_Application_Resource_FrontController).
$plugins = $application->getBootstrap()->getPluginResources();

$application->getBootstrap()->bootstrap();

if (APPLICATION_ENV === 'testing') {
    return;
}

try {
    $application->run();
} catch (Exception $e) {
    echo '<pre>' . $e->getMessage() . '</pre>';exit;
}
