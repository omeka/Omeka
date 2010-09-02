<?php
/**
 * Bootstrap for public interface.
 *
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 **/
 
/**
 * Define the directory and web paths.
 */ 
require_once 'paths.php';

// Define the public theme directory path.
define('THEME_DIR', BASE_DIR . DIRECTORY_SEPARATOR . $site['public_theme']);

$app = new Omeka_Core;
$app->getBootstrap()->setOptions(array(
    'resources' => array(
        'theme' => array(
            'basePath' => THEME_DIR,
            'webBasePath' => WEB_THEME
        )
    )
));
// var_dump($app->getBootstrap());exit;
$app->initialize()->run();