<?php
require_once 'UsersActivations.php';
require_once 'UserTable.php';
require_once 'Entity.php';
require_once 'Item.php';
/**
 * @package Omeka
 **/


class User extends Omeka_Record {

	public $username;
	public $password;
	public $active = '0';
	public $role;
	public $entity_id;
	
	protected $_related = array(
		'Entity'=>'getEntity');
	
	protected function getEntity()
	{
		return $this->getTable('Entity')->find((int) $this->entity_id);
	}
	
	protected function beforeSave()
	{
		$this->Entity->save();
		$this->entity_id = $this->Entity->id;
		
		
		//This part checks the password to see if it has been changed, then encrypts it accordingly
		$db = get_db();
				
		if($this->exists()) {
			$sql = "SELECT password FROM $db->User WHERE id = ?";
			$oldPassword = $db->fetchOne($sql, array((int) $this->id));			
			if($this->password !== $oldPassword) {
				$this->password = sha1($this->password);
			}
		}else {
			
			
			$this->password = sha1($this->password);
		}
	}
	
	protected function beforeSaveForm(&$post)
	{
		if(!$this->processEntity($post)) {
			return false;
		}
		
		/* Permissions check to see if whoever is trying to change role to a super-user*/	
		if(!empty($post['role'])) {
			if($post['role'] == 'super' and !$this->userHasPermission('makeSuperUser')) {
				throw new Omeka_Validator_Exception( 'User may not change permissions to super-user' );
			}
			if(!$this->userHasPermission('changeRole')) {
				throw new Omeka_Validator_Exception('User may not change roles.');
			}
		} 
		
		if($post['active']) {
			$post['active'] = 1;
		}
		//potential security hole
		if(isset($post['password'])) {
			unset($post['password']);
		}
		
		//If the User is not persistent we need to create a placeholder password
		if(!$this->exists()) {
			$this->password = $this->generatePassword(8);
		}		
		
		return true;
	}
	
	/**
	 * @duplication Mostly duplicated in Item::filterInput()
	 *
	 * @return void
	 **/
	protected function filterInput($input)
	{
		$options = array('namespace'=>'Omeka_Filter');
		
		//Alphanumeric with no whitespace allowed, lowercase
		$username_filter = array(new Zend_Filter_Alnum(false), 'StringToLower');
		
		//User form input does not allow HTML tags or superfluous whitespace
		$filters = array(
			'*'				=>	array('StripTags','StringTrim'),
			'username' 		=> 	$username_filter,
			'active' 		=> 	'Boolean');
			
		$filter = new Zend_Filter_Input($filters, null, $input, $options);

		$clean = $filter->getUnescaped();
		
		return $clean;
	}
	
	protected function _validate()
	{
		//Validate the entity of the user
		//This requires special validation within this class b/c the entities themselves have no particular validation.
		if($entity = $this->Entity) {
			
			//Check the name fields to make sure they are filled out
			if(get_class($entity) == 'Person') {
				if(empty($entity->first_name)) {
					
					$this->addError('first_name', 'First name is required for user accounts.' );
				}
				if(empty($entity->last_name)) {
					$this->addError('last_name', 'Last name is required for user accounts.'); 
				}
			}
			//For institutions, only the 'institution' field needs to be filled out
			elseif(get_class($entity) == 'Institution') {
				
				if(empty($entity->institution)) {
					$this->addError('institution', 'Name of the institution is required for user accounts.' );
				}
			}

			if(!Zend_Validate::is($entity->email, 'EmailAddress')) {
				$this->addError('email', 'A valid email address is required for users.');
			}

			if(!$this->emailIsUnique($entity->email)) {
		
				$this->addError('email', 
						'That email address has already been claimed by a different user.  Please notify an administrator if you feel this has been done in error.');			
			}						
		}	
		
		//Validate the role
		if(empty($this->role)) {
			$this->addError('role', 'User must be assigned a valid role.');
		}
		
		//Validate the username
		if(strlen($this->username) < 1 or strlen($this->username) > 30) {
			$this->addError('username', "Username must be no more than 30 characters long");
		}
		
		if(!Zend_Validate::is($this->username, 'Alnum')) {
			$this->addError('username', "Username must be alphanumeric.");
		}
		
		if(!$this->usernameIsUnique($this->username)) {
			$this->addError('username', "'{$this->username}' is already in use.  Please choose another.");
		}
		
		//Validate the password
		$pass = $this->password;
		
		if(empty($pass)) {
			$this->addError('password', "Password must not be empty");
		}elseif(strlen($pass) < 6 or strlen($pass) > 40) {
			$this->addError('password', "Password must be between 6 and 40 characters"); 
		}
	}
	
	/**
	 * This will check the set of IDs for users that have a specific email address.  
	 * If it is greater than 1, or if the 
	 *
	 * @return bool
	 **/
	private function emailIsUnique($email)
	{
		$db = get_db();
		$sql = "SELECT u.id FROM $db->User u INNER JOIN $db->Entity e ON e.id = u.entity_id WHERE e.email = ?";
		
		$id = $db->query($sql, array($email))->fetchAll();
		
		//Either there is nothing stored in the DB yet, 
		//or there is only one and it belongs to this one
		return (!count($id) or ( (count($id) == 1) and ($id[0]['id'] == $this->id) ));
	}
	
	private function usernameIsUnique($username)
	{
		$db = get_db();
		
		$sql = "SELECT u.id FROM $db->User u WHERE u.username = ? LIMIT 1";
		
		$id = $db->fetchOne($sql, array($username));
		
		if($id) {
			//There is an ID and it can't belong to this record
			if(!$this->exists()) {
				return false;
			}
			//There is an ID but it doesn't belong to this record
			elseif($this->exists() and ($this->id != $id) ) {
				return false;
			}
		}
		
		return true;
	}
		
	public function changePassword($new1, $new2, $old)
	{	
		//super users can change the password without knowing the old one
		$current = Omeka::loggedIn();
		if($current->role == 'super') {
			
			if($new1 != $new2) {
				throw new Omeka_Validator_Exception('New password must be typed correctly twice.');
			}
			
			$this->password = $new1;
			
		}else {
			if(empty($new1) || empty($new2) || empty($old)) {
				throw new Omeka_Validator_Exception('User must fill out all password fields in order to change password');
			}
			//If the old passwords don't match up
			if(sha1($old) !== $this->password) {
				throw new Omeka_Validator_Exception('Old password has been entered incorrectly.');
			} 
		
			if($new1 !== $new2) {
				throw new Omeka_Validator_Exception('New password must be typed correctly twice.');
			}	
			
			$this->password = $new1;
		}
	}
	
	protected function processEntity(&$post)
	{	
		$entity = $this->Entity;
		
		//If the entity is new, then determine whether it is an institution or a person
		if(empty($entity)) {
			//Institution provided with no name
			if(empty($post['last_name']) and empty($post['first_name']) and !empty($post['institution'])) {
				require_once 'Institution.php';
				$entity = new Institution;
			}
			else {
				require_once 'Person.php';
				$entity = new Person;
			}
		}
		
		//The new email address is fully legit, so set the entity to the new info				
		$entity->first_name = $post['first_name'];
		$entity->last_name = $post['last_name'];
		$entity->institution = $post['institution'];
		$entity->email = $post['email'];
		
		$this->Entity = $entity;
		
		unset($post['email']);
		unset($post['first_name']);
		unset($post['last_name']);
		unset($post['institution']);
						
		return true;
	}

	/* Generate password. (i.e. jachudru, cupheki) */
	// http://www.zend.com/codex.php?id=215&single=1
	protected function generatePassword($length) 
	{
	    $vowels = array('a', 'e', 'i', 'o', 'u', '1', '2', '3', '4', '5', '6');
	    $cons = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr',
	    'cr', 'br', 'fr', 'th', 'dr', 'ch', 'ph', 'wr', 'st', 'sp', 'sw', 'pr', 'sl', 'cl');

	    $num_vowels = count($vowels);
	    $num_cons = count($cons);
		
		$password = '';
	    while(strlen($password) < $length){
	        $password .= $cons[mt_rand(0, $num_cons - 1)] . $vowels[mt_rand(0, $num_vowels - 1)];
	    }
		$this->password = $password;
		return $password;
	}		
}
?>