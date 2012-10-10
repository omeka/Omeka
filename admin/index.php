<?php
/**
 * Bootstrap for admin interface.
 *
 * This is the same as the public interface bootstrap, except it defines an
 * ADMIN constant used by the bootstrap script to ensure that Omeka loads the 
 * correct view scripts (and any other theme-specific behavior).
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

// Flag this as the admin theme.
define('ADMIN', true);

// Bootstrap the application.
include dirname(dirname(__FILE__)) . '/bootstrap.php';

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
