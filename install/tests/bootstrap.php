<?php
define('APPLICATION_ENV', 'testing');

// NOTE: Must fill this out prior to running tests, because accessing Omeka via 
// CLI results in an invalid WEB_ROOT constant. 
define('WEB_ROOT', '');
if (!WEB_ROOT) {
    die("WEB_ROOT constant must be defined properly for testing purposes.\n");
}
require_once '../constants.php';