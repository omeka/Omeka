<?php
// Ladies and Gentlemen, start your timers
define('APP_START', microtime(true));

require_once 'paths.php';

define('THEME_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['public_theme']);

require_once 'Omeka/Core.php';
$core = new Omeka_Core;
$core->initialize();

// Call the dispatcher which echos the response object automatically
$core->dispatch();

if ((boolean) $config->debug->timer) {
	echo microtime(true) - APP_START;
}
// We're done here.
?>