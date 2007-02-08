<?php
require_once 'Group.php' ;
/**
 * @package Omeka
 * @author Nate Agrin
 * 
 * For use in a ACL type system where each group
 * has specific permission and users belong to the
 * groups.
 **/
class Permission extends Kea_Record { 
    public function setTableDefinition() {
		$this->setTableName('permissions');
		
        $this->hasColumn("name","string",30, "unique|notnull");
		
		$this->hasMany("Group as Groups", "GroupsPermissions.group_id");
    }
}
?>