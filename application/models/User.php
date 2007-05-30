<?php
require_once 'UsersActivations.php';
/**
 * @package Omeka
 **/

class UserListener extends Doctrine_EventListener
{
	/**
	 * @todo should check for $record->getModified() instead of making another SQL query
	 * 
	 * @return void
	 **/
	public function onPreSave(Doctrine_Record $record)
	{
		$conn = Doctrine_Manager::getInstance()->connection();
		
		if($record->exists()) {
			$sql = "SELECT password FROM users WHERE id = {$record->id}";
			$oldPassword = $conn->fetchOne($sql);			
			if($record->password !== $oldPassword) {
				$record->password = sha1($record->password);
			}
		}else {
			$record->password = sha1($record->password);
		}
	}
}

class User extends Kea_Record {

	protected $error_messages = array(	'email' => array('email' => 'Email must be valid', 'unique' => 'That email address has already been claimed by a different user.'),
										'username' => array('unique' => 'That username is already taken.', 'notblank' => 'You must provide a valid username.'));
	
	public function __construct($table = null, $isNewEntry = false)
	{
		parent::__construct($table, $isNewEntry);
		$this->getTable()->setAttribute(Doctrine::ATTR_LISTENER,new UserListener());
	}
	
	public function setUp() {
		$this->ownsMany("ItemsFavorites", "ItemsFavorites.user_id");
		$this->ownsMany("ItemsTags", "ItemsTags.user_id");
		$this->hasMany("Item as Items", "Item.user_id");
		$this->hasMany("Tag as Tags", "ItemsTags.tag_id");
	}
	
    public function setTableDefinition() {
		$this->setTableName('users');
        $this->hasColumn('username', 'string', 30, array('notnull' => true, 'unique'=>true, 'notblank'=>true));
        $this->hasColumn('password', 'string', 40, array('notnull' => true, 'notblank'=>true));
        $this->hasColumn('first_name', 'string', 255, array('notnull' => true, 'default'=>''));
        $this->hasColumn('last_name', 'string', 255, array('notnull' => true, 'default'=>''));
        $this->hasColumn('email', 'string', 255, array('notnull' => true, 'notblank'=>true, 'default'=>'', 'email'=>true, 'unique'=>true));
        $this->hasColumn('institution', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('active', 'boolean', null, array('notnull' => true, 'default'=>'0'));
        $this->hasColumn('role', 'string', 40, array('notnull' => true, 'default'=>'default', 'notblank'=>true));

		$this->index('active', array('fields' => array('active')));
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