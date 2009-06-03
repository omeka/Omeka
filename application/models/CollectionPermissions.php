<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * This will check the ACL To determine whether a user has permission to view 
 * collections that are not public yet then modify the SQL query accordingly
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class CollectionPermissions
{
    public function __construct(Omeka_Db_Select $sql, Omeka_Acl $acl)
    {
        $db = Omeka_Context::getInstance()->getDb();
        
        $has_permission = $acl->checkUserPermission('Collections', 'showNotPublic');
        
        if (!$has_permission) {
            if ($sql->hasJoin('c')) {
                $sql->where("c.public = 1");
            } else {
                throw new Omeka_Record_Exception( "Invalid query provided to CollectionPermissions check" );
            }
        }
    }
}