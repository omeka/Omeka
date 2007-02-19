<?php
require_once 'Zend/Acl.php';
require_once 'Kea/Acl/Role/Registry.php';
class Kea_Acl extends Zend_Acl
{
	/**
	 * I can't believe Zend doesn't have an easy way
	 * of returning an array of the roles in the Acl object
	 * but they don't so here it is!
	 * @author Nate Agrin
	 */
	public function getRoles()
	{
		return $this->_getRoleRegistry()->getRoles();
	}
	
	protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = new Kea_Acl_Role_Registry();
        }
        return $this->_roleRegistry;
    }
}
?>