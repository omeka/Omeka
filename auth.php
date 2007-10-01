<?php 
$authPrefix = get_option('auth_prefix');

//Set up the authentication mechanism with the specially generated prefix
$auth = Zend_Auth::getInstance();

require_once 'Zend/Auth/Storage/Session.php';
$auth->setStorage(new Zend_Auth_Storage_Session($authPrefix));
?>
