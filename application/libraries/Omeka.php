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
	 * Convenience method returns the logged in user
	 * or false depending on whether the user is 
	 * logged in or not.
	 */
        static function loggedIn() {
                require_once 'Zend/Auth.php';
                require_once 'Zend/Session.php';
                require_once 'Omeka/Auth/Adapter.php';
                require_once 'Zend/Filter/Input.php';

                $auth = Zend_Registry::get('auth');
                if ($auth->hasIdentity()) {
                        $user_id = $auth->getIdentity();

                        require_once 'User.php';

                        if(Zend_Registry::isRegistered('logged_in_user')) {
                                return Zend_Registry::get('logged_in_user');
                        }

                        $user = Doctrine_Manager::getInstance()->getTable('User')->find($user_id);

                        Zend_Registry::set('logged_in_user', $user);

                        return $user;
                }
                return false;
        }

}
?>