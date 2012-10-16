<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Mixin for models that have a user that is their "owner."
 * 
 * @package Omeka\Record\Mixin
 */
class Mixin_Owner extends Omeka_Record_Mixin_AbstractMixin
{
    protected $_record;
    protected $_column;
    
    public function __construct($record, $column = 'owner_id')
    {
        parent::__construct($record);
        $this->_column = $column;
    }
    
    public function beforeSave($args)
    {
        // After inserting a record, mark that the logged-in user owns it.
        if ($args['insert']) {
            $column = $this->_column;
            $user = Zend_Registry::get('bootstrap')->getResource('CurrentUser');
            if ($user && !$this->_record->$column) {
                $this->setOwner($user);
            }
        }
    }

    /**
     * Set the record's owner.
     *
     * @param User $user
     */
    public function setOwner(User $user)
    {
        $column = $this->_column;
        $this->_record->$column = $user->id;
    }

    /**
     * Get the record's owner.
     *
     * If the record has no user, this method returns null.
     *
     * @return User|null
     */
    public function getOwner()
    {
        $column = $this->_column;
        $id = $this->_record->$column;
        if (!$id) {
            return null;
        } else {
            return $this->_record->getDb()->getTable('User')->find($id);
        }
    }

    /**
     * Check if the given User owns this record.
     *
     * @param User $user
     * @return boolean
     */
    public function isOwnedBy(User $user)
    {
        $column = $this->_column;
        return $user->id == $this->_record->$column;
    }
}
