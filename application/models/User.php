<?php
require_once 'UsersActivations.php';
require_once 'UsersTable.php';
require_once 'Entity.php';
require_once 'Item.php';
/**
 * @package Omeka
 **/


class User extends Kea_Record {

	protected $error_messages = array(	'email' => array('email' => 'Email must be valid', 'unique' => 'That email address has already been claimed by a different user.'),
										'username' => array('unique' => 'That username is already taken.', 'notblank' => 'You must provide a valid username.'));
	
	public function setUp() {
		$this->hasMany("Item as Items", "Item.user_id");
		$this->ownsOne("Entity", "User.entity_id");
		$this->ownsOne("Entity", "User.entity_id");
	}
	
    public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName('users');
        $this->hasColumn('username', 'string', 30, array('notnull' => true, 'unique'=>true, 'notblank'=>true));
        $this->hasColumn('password', 'string', 40, array('notnull' => true, 'notblank'=>true));

        $this->hasColumn('active', 'boolean', null, array('notnull' => true, 'default'=>'0'));
        $this->hasColumn('role', 'string', 40, array('notnull' => true, 'default'=>'default', 'notblank'=>true));
		$this->hasColumn('entity_id', 'integer', null, array('range'=>array('1')));
		
		$this->index('active', array('fields' => array('active')));
    }
	
	public function preSave()
	{
		$conn = Doctrine_Manager::getInstance()->connection();
				
		if($this->exists()) {
			$sql = "SELECT password FROM users WHERE id = {$this->id}";
			$oldPassword = $conn->fetchOne($sql);			
			if($this->password !== $oldPassword) {
				$this->password = sha1($this->password);
			}
		}else {
			$this->password = sha1($this->password);
		}
	}

	public function get($name) {
		if($this->hasRelation($name)) {
			return parent::get($name);
		}else {
			$entity = $this->Entity;
			if($entity->exists() and $entity->hasRelation($name)) {
				return $entity->$name;
			}
		}
	}
	
	protected function preCommitForm(&$post, $options)
	{
		if(!$this->processEntity($post, $options)) {
			return false;
		}
		
		/* Permissions check to see if whoever is trying to change role to a super-user*/	
		if(!empty($post['role'])) {
			if($post['role'] == 'super' and !$this->userHasPermission('makeSuperUser')) {
				throw new Exception( 'User may not change permissions to super-user' );
			}
			if(!$this->userHasPermission('changeRole')) {
				throw new Exception('User may not change roles.');
			}
		} 
		
		if($post['active']) {
			$post['active'] = 1;
		}
		//potential security hole
		if(isset($post['password'])) {
			unset($post['password']);
		}
				
		
		
		return true;
	}
	
		
	public function changePassword($new1, $new2, $old)
	{	
		//super users can change the password without knowing the old one
		$current = Kea::loggedIn();
		if($current->role == 'super') {
			
			if($new1 != $new2) {
				throw new Exception('New password must be typed correctly twice.');
			}
			
			$this->password = $new1;
			
		}else {
			if(empty($new1) || empty($new2) || empty($old)) {
				throw new Exception('User must fill out all password fields in order to change password');
			}
			//If the old passwords don't match up
			if(sha1($old) !== $this->password) {
				throw new Exception('Old password has been entered incorrectly.');
			} 
		
			if($new1 !== $new2) {
				throw new Exception('New password must be typed correctly twice.');
			}	
			
			$this->password = $new1;
		}
	}
	
	protected function processEntity(&$post, $options)
	{	
		//If the entity is new, then determine whether it is an institution or a person
		if(!$this->Entity->exists()) {
			//Institution provided with no name
			if(empty($post['last_name']) and empty($post['first_name']) and !empty($post['institution'])) {
				require_once 'Institution.php';
				$this->Entity = new Institution;
			}
			else {
				require_once 'Person.php';
				$this->Entity = new Person;
			}
		}
		
		//Check the fields to make sure they are filled out
		if(get_class($this->Entity) == 'Person') {
			if(empty($post['first_name']) or empty($post['last_name'])) {
				throw new Exception( 'First and last name are required for user accounts.' );
			}
		}
		elseif(get_class($this->Entity) == 'Institution') {
			if(empty($post['institution'])) {
				throw new Exception( 'Name of the institution is required for user accounts.' );
			}
		}
		
		require_once 'Zend/Filter/Input.php';
		$clean = new Zend_Filter_Input($post, false);
		
		if(!$clean->testEmail('email')) {
			throw new Exception('A valid email address is required for users.');
		}
				
		//Check for the presence of an email address
		$email = $clean->getRaw('email');
							
		//Branch on persistence
		if(!$this->exists() or ($this->exists() and $email != $this->Entity->email)) {
			
			//Check if email is changed, then verify that it is still unique
			$this->Entity->email = $email;

			if(!$this->Entity->isUnique('email')) {
			
				throw new Exception('This email address is already in use.  Please choose another.');			
			}			
		}

		//The new email address is fully legit, so set the entity to the new info				
		$this->first_name = $post['first_name'];
		$this->last_name = $post['last_name'];
		$this->institution = $post['institution'];
		
		$this->Entity->save();
		unset($post['email']);
		unset($post['first_name']);
		unset($post['last_name']);
		unset($post['institution']);
						
		return true;
	}
	
	public function set($name, $value) {
		if($this->hasRelation($name)) {
			return parent::set($name, $value);
		}elseif($this->Entity->hasRelation($name)) {
			return $this->Entity->set($name, $value);
		}else {
			throw new Exception( $name );
		}
	}
	
	/* Generate password. (i.e. jachudru, cupheki) */
	// http://www.zend.com/codex.php?id=215&single=1
	public function generatePassword($length) 
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