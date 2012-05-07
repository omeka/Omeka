<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * This encapsulates the permissions check for an item.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ItemPermissions
{
    /**
     * Right now SQL must be an instance of Omeka_Db_Select b/c that is the only way to add conditional SQL
     *
     * @return Omeka_Db_Select
     */
    public function __construct(Omeka_Db_Select $sql, Zend_Acl $acl)
    {
        if (!$acl->has('Items')) {
            return;
        }

        $db = Omeka_Context::getInstance()->getDb();
        
        if (!$sql->hasJoin('items')) {
            throw new Omeka_Record_Exception( __('ItemPermissions only works with queries involving Items.') );
        }
        
        $currentUser = Omeka_Context::getInstance()->getCurrentUser();
        $has_permission = $acl->isAllowed($currentUser, 'Items', 'showNotPublic');
        
        $self_permission = $acl->isAllowed($currentUser, 'Items', 'showSelfNotPublic');
        
        if (!$has_permission && !$self_permission) {
            $sql->where('items.public = 1');
        } elseif($has_permission) {
            //Do nothing
        } elseif($self_permission) {
            $sql->where('items.public = 1 OR items.owner_id = ?', $currentUser->id);
        }
    }
}
