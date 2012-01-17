<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Create temporary hashed Urls for purposes of password activation
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class UsersActivations extends Omeka_Record
{
    public $user_id;
    public $url;
    public $added;

    protected $_related = array('User' => 'getUser');

    public static function factory(User $user)
    {
        $ua = new self;
        if (!$user->exists()) {
            throw new RuntimeException(__("Cannot create a user activation for non-existent user."));
        }
        $ua->user_id = $user->id;
        return $ua;
    }

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
