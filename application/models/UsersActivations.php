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
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008 
 **/
class UsersActivations extends Omeka_Record
{
    public $user_id;
    public $url;
    public $added;
    
    protected $_related = array('User' => 'getUser');
        
    protected function beforeSave()
    {
    	$timestamp = microtime(true);
	$this->added = date('Y-m-d H:i:s', $timestamp);
	$this->url = sha1($timestamp);
    }
    
    public function getUser()
    {
        return $this->getDb()->getTable('User')->find($this->user_id);
    }
}
