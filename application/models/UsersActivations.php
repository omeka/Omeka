<?php 
/**
 * Create temporary hashed Urls for purposes of password activation
 *
 * @package Omeka
 * @author CHNM
 **/
class UsersActivations extends Omeka_Record
{
	
	public function setUp()
	{
		$this->hasOne("User", "UsersActivations.user_id");
	}
	
	public function setTableDefinition()
	{
		$this->option('type', 'MYISAM');
		$this->hasColumn("user_id", "integer", null, "notnull");
		$this->hasColumn("url", "string", 100);
		$this->hasColumn("added", "timestamp");
	}
	
	/**
	 * @todo this can have a better algorithm whenever one might feel like it
	 *
	 * @return void
	 **/
	public function generate()
	{
		$this->url = sha1($this->User->password);
	}
} // END class UsersActivations extends Omeka_Record
 ?>
