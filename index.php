<?php
/**
 * Bootstrap for public interface.
 *
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
// Define the directory and web paths.
require_once 'paths.php';

// Define the public theme directory path.
define('THEME_DIR', BASE_DIR . DIRECTORY_SEPARATOR . $site['public_theme']);

$app = new Omeka_Core;
$app->initialize()->run();