<?php 
/**
* This encapsulates the permissions check for an exhibit.
* @todo Find a way to hook this into the plugins
*/
class ExhibitPermissions
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
		
		$has_permission = $acl->checkUserPermission('Exhibits', 'showNotPublic');
		
		if(!$has_permission)
		{
			$sql->where('e.public = 1');
		}
	}
}
 
?>
