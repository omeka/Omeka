<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Extension of Zend_Acl to facilitate batch loading of roles, resources
 * and privileges.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Acl extends Zend_Acl
{
    /**
     * Stored list of roles (circumvents limitations of Zend Framework on storage of roles)
     *
     * @var string
     **/
    protected $_roles = array();
    
    /**
     * A keyed array where the key is the name of the resource,
     * and the value is an array of privileges available for that resource.
     *
     * @var string
     **/
    protected $_resourceList = array();
    
    /**
     * Build the available list of roles, resources/privileges and allowed list from array
     * 
     * @param array
     * @return void
     **/
    public function __construct(array $roles=array(), array $resources=array(), array $allowList=array())
    {
        if($roles) {
            $this->loadRoleList($roles);
        }
        
        if($resources) {
            $this->loadResourceList($resources);         
        }
        
        if($allowList) {
            $this->loadAllowList($allowList);
        }       
    }
    
    /**
     * Load an array of roles into the ACL
     * 
     * @param array
     * @return void
     **/
    public function loadRoleList($roles)
    {
        foreach ($roles as $role) {
             $this->addRole(new Zend_Acl_Role($role));
         }            
         $this->_roles = $roles;        
    }
    
    /**
     * Load an array of resources and privileges into the ACL
     * 
     * @param array Hash keyed to the name of the resource, with values that
     * correspond to arrays of potential privileges for that resource
     * @return void
     **/
    public function loadResourceList($resources)
    {
        foreach ($resources as $resourceName => $privileges) {
             $resource = new Omeka_Acl_Resource($resourceName);
             $resource->add($privileges);
             $this->add($resource);
         }

         $this->_resourceList = array_merge($this->_resourceList, $resources);        
    }
    
    /**
     * 
     * @param array
     * @return void
     **/
    public function loadAllowList(array $allow)
    {
        foreach ($allow as $args) {
            call_user_func_array(array($this, 'allow'), $args);
        }        
    }
    
    /**
     * Retrieve the list of roles in the ACL
     *
     * @return array
     **/
    public function getRoleNames()
    {
        return $this->_roles;
    }
    
    /**
     * Add a role to the ACL.  
     * 
     * @see Zend_Acl::addRole()
     **/
    public function addRole(Zend_Acl_Role_Interface $role, $parents = null)
    {
        $this->_roles[] = $role->getRoleId();
        
        return parent::addRole($role, $parents);
    }
    
    /**
     * Retrieve the set of available resources and privileges for those resources.
     * 
     * @return array Hash where key = name of resource, value = array of privileges for that resource
     **/
    public function getResourceList()
    {
        return $this->_resourceList;
    }
    
    /**
     * Verify that the currently logged in user has permission for a certain
     * resource/privilege combination.
     * 
     * @uses Omeka_Context::getCurrentUser()
     * @param string Name of the resource
     * @param string Name of the privilege
     * @return boolean
     **/    
    public function checkUserPermission($resource, $privilege)
    {
        $user = Omeka_Context::getInstance()->getCurrentUser();
            
        $role = $user ? $user->role : null;
        
        return $this->isAllowed($role, $resource, $privilege);	
    }
}