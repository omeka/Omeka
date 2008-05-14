<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Create temporary hashed Urls for purposes of password activation
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008 
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
		return $this->getDb()->getTable('User')->find($this->user_id);
	}
}