<?php
require_once 'Zend/Auth/Adapter.php';

class Kea_Auth_Adapter extends Zend_Auth_Adapter
{
	public static function staticAuthenticate($options)
	{
		$valid = false;
		$identity = null;
		$message = null;
		
		$user = Doctrine_Manager::connection()->getTable('User')
											  ->findByDql('username LIKE :username AND password LIKE SHA1(:password) AND active = 1', $options);
		
		// The user was logged in correctly
		if (count($user) === 1) {
			$valid = true;
			$user = $user[0];
			
			$user_id = $user->id;
			return new Kea_Auth_Token($valid, $user_id);
		}
		else {
			unset($options['password']);
			$user = Doctrine_Manager::connection()->getTable('User')
												  ->findByDql('username LIKE :username AND active = 1', $options);
			
			if (count($user) === 1) {
				$message = "Invalid password";
			}
			else {
				$message = "Cannot find a user with that username";
			}
			unset($user);
		}
		return new Kea_Auth_Token($valid, $identity, $message);
	}
	
    public function authenticate($options)
    {
		return self::staticAuthenticate($options);
    }
}