<?php
// Bootstrap :: short & swwet
// Compliments CHNM, n8 & Kris
$t1 = microtime(true);
define('KEA_ROOT', dirname(__FILE__));
require_once 'app/config/env.php';
require_once 'Kea/Controller/Front.php';
echo Kea_Controller_Front::run();
echo (microtime(true) - $t1);
// Live and die by the code
?>