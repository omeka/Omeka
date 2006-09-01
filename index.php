<?php
/**
 * Bootstrap
 */
ob_start();
define( 'ABS_ROOT', dirname( __FILE__ ) );
require_once( 'app/config/config.php' );
require_once( 'app/lib/Kea/Front/Controller.php' );
Kea_Front_Controller::run();
?>