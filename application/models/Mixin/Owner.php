<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Mixins
 */
class Mixin_Owner extends Omeka_Record_Mixin
{
    protected $record;
    protected $column;
    
    public function __construct($record, $column = 'owner_id')
    {
        $this->record = $record;
        $this->column = $column;
    }
    
    /**
     * After inserting a record, mark that the logged-in user owns it.
     */
    public function beforeInsert()
    {
        $column = $this->column;
        $user = Omeka_Context::getInstance()->getCurrentUser();
        if ($user && !$this->record->$column) {
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
        $column = $this->column;
        $this->record->$column = $user->id;
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
        $column = $this->column;
        $id = $this->record->$column;
        if (!$id) {
            return null;
        } else {
            return $this->getDb()->getTable('User')->find($id);
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
        $column = $this->column;
        return $user->id == $this->record->$column;
    }
}
