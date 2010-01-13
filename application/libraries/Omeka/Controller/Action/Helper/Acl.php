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
     * User record corresponding to the logged-in user, otherwise null.
     * 
     * @var User|null
     */
    protected $_currentUser;

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
    public function __construct($acl, $currentUser)
    {
        $this->_acl = $acl;
        $this->_currentUser = $currentUser;
    }
    
    public function preDispatch()
    {
        if (!$this->isAllowed($this->getRequest()->getActionName())) {
            $this->getRequest()->setControllerName('error')
                               ->setActionName('forbidden')
                               ->setModuleName('default')
                               ->setDispatched(false);
        }
    }
    	
	/**
	 * Notifies whether the logged-in user has permission for a given resource/
	 * privilege combination.
	 *
	 * If an ACL resource being checked has not been defined, access to that 
	 * resource should not be controlled.  This allows plugin writers to 
	 * implement controllers without also requiring them to be aware of the ACL. 
	 * 
	 * Conversely, in the event that an ACL resource has been defined, all access
	 * permissions for that controller must be properly defined.
	 * 
	 * The names of resources should correspond to the name of the controller 
	 * class minus 'Controller', e.g. 
	 * Geolocation_IndexController -> 'Geolocation_Index'
	 * CollectionsController -> 'Collections'
	 * 
	 * @param string $privilege
	 * @param Zend_Acl_Resource|string|null If null, the 
	 * @see getResourceName()
	 * @return boolean
	 **/
	public function isAllowed($privilege, $resource=null) 
	{
		$allowed = $this->_allowed;
		if(isset($allowed[$privilege])) {
			return $allowed[$privilege];
		}
		
		if(!$resource) {
			$resource = $this->getResourceName();
		}

		// If the resource has not been defined in the ACL, allow access to the
		// controller.
		if (!$this->_acl->has($resource)) {
		    return true;
		}
		
		return $this->_acl->isAllowed($this->_currentUser, $resource, $privilege);
	}
    
    /**
     * Retrieve the name of the ACL resource based on the name of the controller 
     * and, if not the default module, the name of the module.
     *  
     * @todo Should use the Zend inflection, though mine works better at the moment [KBK].
     * @return string
     **/
    public function getResourceName()
    {
        $controllerName = $this->getRequest()->getControllerName();
        $moduleName     = $this->getRequest()->getModuleName();
        
        // This ZF inflector should work but it doesn't!
        // $inflector = new Zend_Filter_Word_DashToCamelCase();
        // return $inflector->filter($controllerName);
        // Instead we are going to inflect from dashed-lowercase to CamelCase.
        $inflectedControllerName = implode('', array_map('ucwords', explode('-', $controllerName)));
        
        if ('default' == $moduleName) {
            // This is the default moduloe, so there is no need to add a 
            // namespace to the resource name.
            $resourceName = $inflectedControllerName;
        } else {
            // This is not a default module (i.e. plugin), so we need to add a 
            // namespace to the resource name.
            $inflectedModuleName = implode('', array_map('ucwords', explode('-', $moduleName)));
            $resourceName = $inflectedModuleName . '_' . $inflectedControllerName;
        }
        return $resourceName;
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
