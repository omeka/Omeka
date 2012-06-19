<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Mixin for models that have a user that is their "owner."
 * 
 * @package Omeka
 * @subpackage Mixins
 */
class Mixin_Owner extends Omeka_Record_Mixin
{
    protected $_record;
    protected $_column;
    
    public function __construct($record, $column = 'owner_id')
    {
        parent::__construct($record);
        $this->_column = $column;
    }
    
    /**
     * After inserting a record, mark that the logged-in user owns it.
     */
    public function beforeInsert()
    {
        $column = $this->_column;
        $user = Omeka_Context::getInstance()->getCurrentUser();
        if ($user && !$this->_record->$column) {
            $this->setOwner($user);
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
