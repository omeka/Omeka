<?php

/**
 * Groups_Permissions join table
 *
 * @package default
 * @author Nate Agrin
 **/
class GroupsPermissions extends Kea_Record
{
	public function setUp() {
		$this->hasOne("Group", "GroupsPermissions.group_id");
		$this->hasOne("Permission", "GroupsPermissions.permission_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("group_id", "integer", null, "notnull");
		$this->hasColumn("permission_id", "integer", null, "notnull");
	}
	
	public function validate() {
		$preExisting = $this->getTable()->findBySql("group_id = ? AND permission_id = ?", array($this->group_id, $this->permission_id));
		if($preExisting && $it = $preExisting->getFirst()) {
			//Is there a better way to compare an object with its referent in the database?
			if($it->obtainIdentifier() != $this->obtainIdentifier()) {
				$this->getErrorStack()->add('group_id', 'duplicate');
			}
		}
	}

	
} // END class GroupsPermissions

?>