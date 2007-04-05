<?php
require_once 'Group.php' ;
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

/**
 * @todo generate random password for new users (find code in old sitebuilder)
 * @todo Email should validate to email
 */
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
		$this->hasOne("Group", "User.group_id");
		$this->hasMany("Tag as Tags", "ItemsTags.tag_id");
	}
	
    public function setTableDefinition() {
		$this->setTableName('users');
        $this->hasColumn("username","string",30, "unique|notblank");
        $this->hasColumn("password","string",40, "notblank");
        $this->hasColumn("first_name","string",200);
        $this->hasColumn("last_name","string",200);
		$this->hasColumn("email", "string", 200, "email|unique");
        $this->hasColumn("institution","string",300);
        $this->hasColumn("active","boolean",1);
		$this->hasColumn("group_id", "integer");
		$this->index('active', array('fields' => array('active')));
		$this->index('group', array('fields' => array('group_id')));
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