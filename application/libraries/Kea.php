<?php
/**
 * @package Kea
 * 
 */
final class Kea
{
	static function autoload($classname)
	{
		if (class_exists($classname)) {
			return false;
		}

		$path = dirname(__FILE__);
		$class = $path . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR,$classname) . '.php';

		if (file_exists($class)) {
			require_once $class;
			return;
		}

		return false;
	}
	
	/**
	 * Convenience method returns the logged in user
	 * or false depending on whether the user is 
	 * logged in or not.
	 */
	static function loggedIn() {
		require_once 'Zend/Auth.php';
		require_once 'Zend/Session.php';
		require_once 'Kea/Auth/Adapter.php';
		require_once 'Zend/Filter/Input.php';

		$auth = Zend::Registry('auth');
		if ($auth->isLoggedIn()) {
			$token = $auth->getToken();
			return $token->getIdentity();
		}
		return false;
	}
}
?>