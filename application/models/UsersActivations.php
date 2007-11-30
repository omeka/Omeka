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
		
	protected function beforeSave()
	{
		$this->added = microtime(true);
		$this->url = sha1(microtime(true));
	}
} // END class UsersActivations extends Omeka_Record
 ?>
