<?php
require_once 'Group.php' ;
/**
 * @package Omeka
 **/

/**
 * @author Nate Agrin
 */
class UserListener extends Doctrine_EventListener
{
	public function onPreSave(Doctrine_Record $record)
	{
		$record->password = sha1($record->password);
	}
	
	public function onPreCreate(Doctrine_Record $record) {}
}

/**
 * @author Kris Kelly
 */
class User extends Kea_Record {

	public function __construct($table = null, $isNewEntry = false)
	{
		parent::__construct($table, $isNewEntry);
		$this->getTable()->setAttribute(Doctrine::ATTR_LISTENER,new UserListener());
	}
	
    public function setTableDefinition() {
		$this->setTableName('users');
		
		$this->hasOne("Group", "User.group_id");
		
        $this->hasColumn("name","string",30, "unique|notnull");
        $this->hasColumn("username","string",30);
        $this->hasColumn("password","string",40);
        $this->hasColumn("first_name","string",200);
        $this->hasColumn("last_name","string",200);
		$this->hasColumn("email", "string", 200);
        $this->hasColumn("institution","string",300);
        $this->hasColumn("active","boolean",1);
		$this->hasColumn("group_id", "integer");
    }
}
?>