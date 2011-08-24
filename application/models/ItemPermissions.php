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
    public function __construct(Omeka_Db_Select $sql, Omeka_Acl $acl)
    {
        if (!$acl->has('Items')) {
            return;
        }

        $db = Omeka_Context::getInstance()->getDb();
        
        if (!$sql->hasJoin('i')) {
            throw new Omeka_Record_Exception( __("This SQL statement needs a FROM or JOIN clause equivalent to '%s i' in order to have permissions checked!", $db->Item) );
        }
        
        $has_permission = $acl->checkUserPermission('Items', 'showNotPublic');
        
        $self_permission = $acl->checkUserPermission('Items', 'showSelfNotPublic');
        
        if (!$has_permission && !$self_permission) {
            $sql->where('i.public = 1');
        } elseif($has_permission) {
            //Do nothing
        } elseif($self_permission) {
            
            //Show both public items and items added or modified by a specific user
            
            //The slow way (correlated tables)
            /*
            $sql->joinLeft('entities_relations ie', 'ie.relation_id = i.id');
            $sql->joinLeft('entities e', 'e.id = ie.entity_id');
            $sql->joinLeft('users u', 'u.entity_id = e.id');
            $sql->joinLeft('entity_relationships ier', 'ier.id = ie.relationship_id');
            $sql->where( '(i.public = 1 OR (u.id = ? AND (ier.name = "added" OR ier.name = "modified") AND ie.type = "Item") )', $user->id);
            */    
            //This appears to be faster but I left in the old code just in case it works better
            $user = Omeka_Context::getInstance()->getCurrentUser();
            $expr = new Zend_Db_Expr("
            (SELECT er_perm.relation_id as item_id
            FROM $db->EntitiesRelations er_perm
            INNER JOIN $db->Entity e ON e.id = er_perm.entity_id
            INNER JOIN $db->User u ON u.entity_id = e.id 
            INNER JOIN $db->Item i ON i.id = er_perm.relation_id
            LEFT JOIN $db->EntityRelationships ier ON ier.id = er_perm.relationship_id
            WHERE   
                (u.id = '{$user->id}' 
                AND (ier.name = \"added\" OR ier.name = \"modified\") 
                AND er_perm.type = \"Item\"
                ) 
            OR i.public = 1)");
            
            $sql->joinInner(array('i_perm'=>$expr), "i_perm.item_id = i.id", array());
        }
    }
}
