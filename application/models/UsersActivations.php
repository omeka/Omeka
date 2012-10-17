<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A user activation and its metadata.
 * 
 * @package Omeka\Record
 */
class UsersActivations extends Omeka_Record_AbstractRecord
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

    protected function beforeSave($args)
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
