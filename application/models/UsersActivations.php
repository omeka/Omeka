<?php 
/**
 * Create temporary hashed Urls for purposes of password activation
 *
 * @package Omeka
 * @author CHNM
 **/
class UsersActivations extends Omeka_Record
{
	public $user_id;
	public $url;
	public $added;
	
	protected $_related = array('User'=>'getUser');
		
	protected function beforeSave()
	{
		$this->added = microtime(true);
		$this->url = sha1(microtime(true));
	}
	
	public function getUser()
	{
		return get_db()->getTable('User')->find($this->user_id);
	}
} // END class UsersActivations extends Omeka_Record
 ?>
