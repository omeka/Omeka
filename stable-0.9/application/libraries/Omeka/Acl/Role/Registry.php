<?php
require_once 'Zend/Acl/Role/Registry.php';
class Omeka_Acl_Role_Registry extends Zend_Acl_Role_Registry
{	
	public function getRoles()
	{
		return $this->_roles;
	}
}
?>