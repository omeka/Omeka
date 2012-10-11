<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package Omeka
 */
 
// Flag this as the admin theme.
define('ADMIN', true);

// Bootstrap the application.
include dirname(dirname(__FILE__)) . '/bootstrap.php';

// Configure, initialize, and run the application.
$application = new Omeka_Application(APPLICATION_ENV);
$application->getBootstrap()->setOptions(array(
    'resources' => array(
        'theme' => array(
            'basePath' => THEME_DIR,
            'webBasePath' => WEB_THEME
        )
    )
));
// Set an admin flag to the front controller.
Zend_Controller_Front::getInstance()->setParam('admin', true);
$application->initialize()->run();
