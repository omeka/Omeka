<?php

/**
 * Groups_Permissions join table
 *
 * @package default
 * 
 **/
class GroupsPermissions extends Kea_JoinRecord
{
	public function setUp() {
		$this->hasOne("Group", "GroupsPermissions.group_id");
		$this->hasOne("Permission", "GroupsPermissions.permission_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("group_id", "integer", null, "notnull");
		$this->hasColumn("permission_id", "integer", null, "notnull");
	}
	
} // END class GroupsPermissions

?>