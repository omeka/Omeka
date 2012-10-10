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
include dirname(dirname(__FILE__)) . '/bootstrap.php';
