<?php 
/**
* CollectionPermissions
*
* This will check the ACL To determine whether a user has permission to view collections that are not public yet 
* then modify the SQL query accordingly
*/
class CollectionPermissions
{
	public function __construct(Omeka_Select $sql)
	{
		$acl = get_acl();
		$db = get_db();
		
		$has_permission = $acl->checkUserPermission('Collections', 'showNotPublic');
		
		if(!$has_permission)
		{
			if($sql->hasFrom("$db->Collection c") or $sql->hasJoin("$db->Collection c")) {
				$sql->where("c.public = 1");
			}
			else {
				throw new Exception( "Invalid query provided to CollectionPermissions check" );
			}
		}
	}
}
 
?>
