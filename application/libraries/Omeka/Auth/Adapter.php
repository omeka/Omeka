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
		$messages = array();
		
		$options = array($this->username, $this->password);
			
		$db = get_db();
		$sql = "SELECT u.id FROM {$db->User} u WHERE u.username LIKE ? AND password LIKE SHA1(?) AND active = 1 LIMIT 1";
		
		$user_id = (int) $db->fetchOne($sql, $options);

		// The user was logged in correctly
		if ($user_id) {
			$valid = true;
			$result = new Zend_Auth_Result($valid, $user_id);
			return $result;
		}
		else {
			unset($options['password']);
			
			$sql = "SELECT u.id FROM {$db->User} u WHERE u.username LIKE ? AND active = 1 LIMIT 1";
			
			$user_id = (int) $db->fetchOne($sql, array($this->username));
			
			if ($user_id) {
				$messages[] = "Invalid password";
			}
			else {
				$messages[] = "Cannot find a user with that username";
			}
		}
		return new Zend_Auth_Result($valid, $identity, $messages);
	}
}