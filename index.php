<?php
/**
 * Bootstrap the public interface.
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Bootstrap the application.
require_once 'bootstrap.php';

// Configure the bootstrap and run the application.
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
