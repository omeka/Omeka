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
		$timestamp = microtime(true);
		$this->added = date('Y-m-d H:i:s', $timestamp);
		$this->url = sha1($timestamp);
	}
	
	public function getUser()
	{
		return get_db()->getTable('User')->find($this->user_id);
	}
} // END class UsersActivations extends Omeka_Record
 ?>
