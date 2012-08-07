<?php
/**
 * Bootstrap for public interface.
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Define the directory and web paths.
 */ 
require_once 'paths.php';

// Define the public theme directory path.
define('THEME_DIR', PUBLIC_THEME_DIR);

$application = new Omeka_Application(APPLICATION_ENV);
$application->getBootstrap()->setOptions(array(
    'resources' => array(
        'theme' => array(
            'basePath' => THEME_DIR,
            'webBasePath' => WEB_THEME
        )
    )
));
$application->initialize()->run();
