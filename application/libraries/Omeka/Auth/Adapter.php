<?php
require_once 'Zend/Auth/Adapter/Interface.php';

class Omeka_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
	public function __construct($username, $password)
    {
        $this->username = $username;
		$this->password = $password;
    }
	
	public function authenticate()
	{
		$valid = false;
		$identity = null;
		$message = null;
		
		$options = array('username'=>$this->username, 'password'=>$this->password);
		
		$user = Doctrine_Manager::connection()->getTable('User')
											  ->findByDql('username LIKE :username AND password LIKE SHA1(:password) AND active = 1', $options);
		
		// The user was logged in correctly
		if (count($user) === 1) {
			$valid = true;
			$user = $user[0];
			
			$user_id = $user->id;
			return new Zend_Auth_Result($valid, $user_id);
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
		return new Zend_Auth_Result($valid, $identity, $message);
	}
}