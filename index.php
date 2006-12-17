<?php
/**
 * Bootstrap
 */
$t1 = microtime(true);

define('KEA_ROOT', dirname(__FILE__));
require_once 'app/config/env.php';
require_once 'Kea/Controller/Front.php';
require_once 'Kea/View/Theme.php';
Kea_Controller_Front::setView(new Kea_View_Theme);
echo Kea_Controller_Front::run();

echo (microtime(true) - $t1);
?>