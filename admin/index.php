<?php
// Ladies and Gentlemen, start your timers
define('APP_START', microtime(true));

// include the paths and define a theme path
include '../paths.php';
define('THEME_DIR', ADMIN_DIR.DIRECTORY_SEPARATOR.'themes');
define('PUBLIC_THEME_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'themes');

require_once 'Omeka/Core.php';
$core = new Omeka_Core;
$core->initialize();

#############################################
# HERE IS WHERE WE SET THE ADMIN SWITCH
#############################################
$core->getRequest()->setParam('admin', true);
#############################################
# END ADMIN SWITCH
#############################################

#############################################
# CHECKING TO SEE IF THE USER IS LOGGED IN IS HANDLED BY
# THE Omeka_Controller_Action::preDispatch() method
#############################################

#############################################
# DISPATCH THE REQUEST, AND DO SOMETHING WITH THE OUTPUT
#############################################
$core->dispatch();

if ((boolean) $config->debug->timer) {
	echo microtime(true) - APP_START;
}

if(isset($config->log->sql) && $config->log->sql) {
	$logger->logQueryTotal();
}

// We're done here.
?>