<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @access private
 */

/**
 * This will check the ACL To determine whether a user has permission to view 
 * collections that are not public yet then modify the SQL query accordingly
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class CollectionPermissions
{
    public function __construct(Omeka_Db_Select $sql, Zend_Acl $acl)
    {
        if (!$acl->has('Collections')) {
            return;
        }

        $db = Omeka_Context::getInstance()->getDb();

        if (!$sql->hasJoin('c')) {
            throw new Omeka_Record_Exception( __("Invalid query provided to CollectionPermissions check") );
        }

        $currentUser = Omeka_Context::getInstance()->getCurrentUser();        
        $has_permission = $acl->isAllowed($currentUser, 'Collections', 'showNotPublic');
        $self_permission = $acl->isAllowed($currentUser, 'Collections', 'showSelfNotPublic');

        if (!$has_permission && !$self_permission) {
            $sql->where('c.public = 1');
        } elseif($has_permission) {
            //Do nothing
        } elseif($self_permission) {
            $sql->where('c.public = 1 OR c.owner_id = ?', $currentUser->id);
        }
    }
}
