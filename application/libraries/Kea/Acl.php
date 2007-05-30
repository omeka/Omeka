<?php
require_once 'Zend/Acl.php';
require_once 'Kea/Acl/Role/Registry.php';
class Kea_Acl extends Zend_Acl
{
	protected $_autosave = true;
	
	/**
	 * Zend doesn't have a way of getting what rules/permissions are potentially
	 * available for specific resources or for global resources, so here is an
	 * attempt to implement something along those lines.
	 * 
	 * Zend_Acl has a $_rules array that stores the implemented rules,
	 * this stores potentially implementable rules.
	 * 
	 * GLOBAL is a the array that holds the global permission names.
	 * Other resources can be defined by plugin writers, but will generally
	 * take the prefix of 'CONTROLLER_NAME' => array().
	 */
	protected $_permissions = array('GLOBAL' => array());
	
	public function resourceHasRule($resource,$rule) {
		$rules = $this->_permissions[$resource];
		if(!$rules) return false;
		return in_array($rule,$rules);
	}
	
	public function getRules() {
		return $this->_permissions;
	}
	
	public function deleteRules() {
		$this->_permissions = array('GLOBAL' => array());
		$this->autoSave();
	}
	
	public function registerRule(Zend_Acl_Resource_Interface $resource = null, $permissions) {
		$permissions = (array) $permissions;
		
		// Register a Global permission
		if ($resource === null) {
			foreach($permissions as $permission) {
				if (!in_array($permission, $this->_permissions['GLOBAL'])) {
					$this->_permissions['GLOBAL'][] = $permission;
				}
			}
		}
		
		// Register a resource dependent permission
		else {
			// Does the ACL already have the resource?
			if (!$this->has($resource)) $this->add($resource);
			
			// Does the resource array need to be created?
			$resourceName = $resource->getResourceId();
			if (!isset($this->_permissions[$resourceName])) $this->_permissions[$resourceName] = array();
			
			foreach($permissions as $permission) {
				if (!in_array($permission, $this->_permissions[$resourceName])) {
					$this->_permissions[$resourceName][] = $permission;
				}
			}
		}

		// Auto save the acl object
		$this->autoSave();
	}

	public function removeRule($resource = null, $permissions)
	{
		$permissions = (array) $permissions;
		
		// Remove a global permission
		if ($resource === null) {
			foreach($permissions as $permission) {
				$key = array_search($permission, $this->_permissions['GLOBAL']);
				if ($key !== false) {
					unset($this->_permissions['GLOBAL'][$key]);
				}
			}
		}
		
		// Remove a resource specific permission
		else {
			if ($resource instanceof Zend_Acl_Resource_Interface) {
	            $resourceName = $resource->getResourceId();
	        } else {
	            $resourceName = (string) $resource;
	        }
			
			// Does the ACL already have the resource?
			if (!$this->has($resourceName)) return false;
			
			// Does the resource array even exist?
			if (!isset($this->_permissions[$resourceName])) {
				throw new Zend_Acl_Exception('The ACL has the resource '.$resourceName.' but it is not registered in the permissions array.');
			}
			
			// Remove the permission
			foreach ($permissions as $permission) {
				$key = array_search($permission, $this->_permissions[$resourceName]);
				if ($key !== false) {
					unset($this->_permissions[$resourceName][$key]);
				}
			}
			
			// If the resource is out of permissions, get rid of it
			if (count($this->_permissions[$resourceName]) == 0) {
				unset($this->_permissions[$resourceName]);
				$this->remove($resourceName);
			}
		}
		
		// Auto save the acl object
		$this->autoSave();
	}
	
	public function removeRulesByResource($resource)
	{
		if ($resource instanceof Zend_Acl_Resource_Interface) {
            $resourceId = $resource->getResourceId();
        } else {
            $resourceId = (string) $resource;
        }
		
		if (!$this->has($resourceId)) return false;
		
		if (!isset($this->_permissions[$resourceId])) {
			throw new Zend_Acl_Exception('The ACL has the resource '.$resourceId.' but it is not registered in the permissions array.');
		}

		unset($this->_permissions[$resourceId]);
		$this->remove($resourceId);
		$this->autoSave();
	}
	
	/**
	 * Takes a single role and return an
	 * array of what that role is allowed.  This is a purposefully simplified
	 * method of the Zend ACL operations
	 */
	public function getRoleAssignedRules($role) {
		$roleRules = array('GLOBAL' => array());

		$globalRules = $this->_rules['allResources']['byRoleId'][$role]['byPrivilegeId'];
		if(!empty($globalRules)) {
			foreach($globalRules as $rule => $settings) {
				$roleRules['GLOBAL'][] = $rule;
			}			
		}


		foreach ($this->_rules['byResourceId'] as $resourceId => $settings) {
			foreach ($settings['byRoleId'] as $roleId => $permissions) {
				if ($role == $roleId) {
					$roleRules[$resourceId] = array();
					foreach ($permissions['byPrivilegeId'] as $permission => $allow) {
						$roleRules[$resourceId][] = $permission;
					}
				}
			}
		}
		return $roleRules;
	}
	
	public function removeRulesByRole($role) {
		$rules = $this->getRoleAssignedRules($role);
		foreach($rules as $resource => $rule) {
			if ($resource == 'GLOBAL') {
				$this->removeAllow($role, null, $rule);	
			}
			else {
				$this->removeAllow($role, $resource, $rule);
			}
		}
		$this->autoSave();
	}
	
	/**
	 * I can't believe Zend doesn't have an easy way
	 * of returning an array of the roles in the Acl object
	 * but they don't so here it is!
	 */
	public function getRoles()
	{
		return $this->_getRoleRegistry()->getRoles();
	}
	
	public function getResources()
	{
		return $this->_resources;
	}
	
	protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = new Kea_Acl_Role_Registry();
        }
        return $this->_roleRegistry;
    }

	public function save()
	{
		$optionTable = Doctrine_Manager::getInstance()->getTable('option');;
		$acl = $optionTable->findByDql("name LIKE :name", array('name' => 'acl'));
		$acl[0]->value = serialize($this);
		$acl[0]->save();
	}
	
	public function isAllowed($role = null, $resource = null, $privilege = null){
		// A global permission check is occuring
		if ($resource === null && $privilege !== null) {
			$rules =& $this->_getRules();
			print_r($rules);
		}
		// A resource permission check is occuring
		elseif ($resource !== null && $privilege !== null) {
			
		}
		return parent::isAllowed($role, $resource, $privilege);
	}
	
	public function setAutoSave($bool) 
	{
		$this->_autosave = $bool;
	}
	
	public function autoSave()
	{
		if($this->_autosave) {
			$this->save();
		}
	}
}
?>