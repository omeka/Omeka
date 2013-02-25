<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An activation code for a User.
 * 
 * @package Omeka\Record
 */
class UsersActivations extends Omeka_Record_AbstractRecord
{
    /**
     * The ID of the User this activation code is for.
     *
     * @var int
     */
    public $user_id;

    /**
     * Random activation key.
     *
     * @var string
     */
    public $url;

    /**
     * Date this activation key was created.
     *
     * @var string
     */
    public $added;

    /**
     * Related records.
     *
     * @var array
     */
    protected $_related = array('User' => 'getUser');

    /**
     * Get a new UsersActivations for a User.
     *
     * @param User $user
     * @return UsersActivations
     */
    public static function factory(User $user)
    {
        $ua = new self;
        if (!$user->exists()) {
            throw new RuntimeException(__("Cannot create a user activation for non-existent user."));
        }
        $ua->user_id = $user->id;
        return $ua;
    }

    /**
     * Before-save hook.
     *
     * Set the timestamp and create a random key.
     */
    protected function beforeSave($args)
    {
        $timestamp = microtime(true);
        $this->added = date('Y-m-d H:i:s', $timestamp);
        $this->url = sha1($timestamp);
    }

    /**
     * Get the User for this Activation.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->getDb()->getTable('User')->find($this->user_id);
    }
}
