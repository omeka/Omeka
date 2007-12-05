<?php
/**
 * @package Omeka
 * 
 */
final class Omeka
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
	 * Replace _ with spaces and capitalize words
	 *
	 * @return string
	 **/	
	static function humanize($value)
	{
		return ucwords(strtolower(str_replace('_', ' ', $value)));
	}
	
	/**
	 * Convenience method returns the logged in user
	 * or false depending on whether the user is 
	 * logged in or not.
	 */
        static function loggedIn() {
           if(Zend_Registry::isRegistered('logged_in_user')) {
				return Zend_Registry::get('logged_in_user');
			}

     	require_once 'Zend/Auth.php';
        require_once 'Zend/Session.php';
        require_once 'Omeka/Auth/Adapter.php';
        require_once 'Zend/Filter/Input.php';

		$auth = Zend_Registry::get('auth');
		if ($auth->hasIdentity()) {
			$user_id = $auth->getIdentity();
			
			require_once 'User.php';
			
			
			
			$user = get_db()->getTable('User')->find($user_id);
			
			Zend_Registry::set('logged_in_user', $user);
			
			return $user;
		} 
		//Should also cache the negative response so we don't ping $auth->getIdentity() a million times
		else {
			Zend_Registry::set('logged_in_user', false);
		}
		return false;
	}
}
?>