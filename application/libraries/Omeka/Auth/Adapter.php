<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Zend_Auth_Adapter_Interface
 */ 
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * Used for authenticating users against the Omeka database.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    protected $_db;
    
	public function __construct($username, $password, $database)
    {
        $this->username = $username;
		$this->password = $password;
		$this->_db = $database;
    }
	
	public function authenticate()
	{
		$valid = false;
		$identity = null;
		$messages = array();
		
		$options = array($this->username, $this->password);
			
		$db = $this->_db;
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