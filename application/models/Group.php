<?php
require_once 'GroupsPermissions.php';
require_once 'User.php';
require_once 'Permission.php';
/**
 * @package Sitebuilder
 * @author Nate Agrin
 * 
 * For use in a ACL type system where each group
 * has specific permission and users belong to the
 * groups.
 **/
class Group extends Kea_Record { 
    public function setTableDefinition() {
		$this->setTableName('groups');
		
        $this->hasColumn("name","string",30, "unique|notnull");
		$this->hasColumn("group_id", "integer");

		$this->hasOne("Group as Groups", "Group.group_id");
		$this->hasMany("Permission as Permissions", "GroupsPermissions.permission_id");
		$this->ownsMany("User as Users", "User.group_id");
    }
}
?>