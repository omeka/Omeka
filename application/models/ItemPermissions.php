<?php 
/**
* This encapsulates the permissions check for an item.
* @todo Find a way to hook this into the plugins
*/
class ItemPermissions
{
	/**
	 * Right now SQL must be an instance of Omeka_Select b/c that is the only way to add conditional SQL
	 *
	 * @return Omeka_Select
	 **/
	public function __construct(Omeka_Select $sql)
	{
		$acl = get_acl();
		$db = get_db();
		
		if(!$sql->hasFrom("$db->Item i") and !$sql->hasJoin("$db->Item i")) {
			throw new Exception( "This SQL statement needs a FROM clause equivalent to '$db->Item i' in order to have permissions checked" );
		}
		
		$has_permission = $acl->checkUserPermission('Items', 'showNotPublic');

		$self_permission = $acl->checkUserPermission('Items', 'showSelfNotPublic');

		if(!$has_permission and !$self_permission) {
			$sql->where('i.public = 1');
		}
		elseif($has_permission) {
			//Do nothing
		}elseif($self_permission) {
			
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
			
			$user = Omeka::loggedIn();
			
			$sql->innerJoin("(SELECT er_perm.relation_id as item_id
			FROM $db->EntitiesRelations er_perm
			INNER JOIN $db->Entity e ON e.id = er_perm.entity_id
			INNER JOIN $db->User u ON u.entity_id = e.id 
			INNER JOIN $db->Item i ON i.id = er_perm.relation_id
			LEFT JOIN $db->EntityRelationships ier ON ier.id = er_perm.relationship_id
			WHERE
			(u.id = '{$user->id}' AND (ier.name = \"added\" OR ier.name = \"modified\") AND er_perm.type = \"Item\") OR i.public = 1) i_perm", 
			"i_perm.item_id = i.id");
	
		}
	}
}
 
?>
