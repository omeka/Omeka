<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Leverages the ACL to automatically check permissions for the current controller/action combo
 *
 * @uses Omeka_Acl
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Omeka_Acl
	 *
	 * @var Omeka_Acl
	 **/
    protected $_acl;

	/**
	 * Temporarily allowed permissions
	 *
	 * @var array
	 **/
	protected $_allowed = array();
    
    /**
     * Instantiated with the ACL permissions which will be used to verify permission levels
     * 
     * @param Omeka_Acl
     * @return void
     **/
    public function __construct($acl)
    {
        $this->_acl = $acl;
    }
    
    public function preDispatch()
    {
        $this->checkActionPermission($this->getRequest()->getActionName());
    }
    
	protected function checkActionPermission($action)
	{   
		//Here is the permissions check for each action
		try {
			if(!$this->isAllowed($action)) {		
                throw new Omeka_Controller_Exception_403();
			}
		} 
		//Silence exceptions that occur when an action has no equivalent privilege in the ACL
		catch (Zend_Acl_Exception $e) {}		
	}
	
	/**
	 * Notifies whether the logged-in user has permission for the given privilege
	 * i.e., if the $privilege is 'edit', then this will return TRUE if the user has permission to 'edit' for 
	 * the current controller
	 *
	 * @return bool
	 **/
	public function isAllowed($privilege, $resource=null) 
	{
		$allowed = $this->_allowed;
		if(isset($allowed[$privilege])) {
			return $allowed[$privilege];
		}
		
		if(!$resource) {
			$resource = ucwords($this->getRequest()->getControllerName());
		}
		
		//If the resource exists (Controller) but the tested privilege (action) has not been defined in the ACL, then allow access
        if(($resource = $this->_acl->get($resource)) && (!$resource->has($privilege))) {
            return true;
        }
		
		return $this->_acl->checkUserPermission($resource, $privilege);
	}
    
	/**
	 * Temporarily override the ACL's permissions for this controller
	 *
	 **/
	public function setAllowed($rule,$isAllowed=true) 
	{
		$this->_allowed[$rule] = $isAllowed;
	}    
}
