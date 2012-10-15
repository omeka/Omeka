<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Leverages the ACL to automatically check permissions for the current
 * controller/action combo.
 * 
 * @package Omeka\Controller\ActionHelper
 */
class Omeka_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * ACL object.
     *
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * User record corresponding to the logged-in user, otherwise null.
     *
     * @var User|null
     */
    protected $_currentUser;

    /**
     * Temporarily allowed permissions.
     *
     * @var array
     */
    protected $_allowed = array();

    /**
     * Whether we should automatically try to set the resource object.
     *
     * @var boolean
     */
    protected $_autoloadResourceObject = true;

    /**
     * Instantiated with the ACL permissions which will be used to verify
     * permission levels.
     *
     * @param Zend_Acl $acl
     */
    public function __construct($acl, $currentUser)
    {
        $this->_acl = $acl;
        $this->setCurrentUser($currentUser);
    }

    /**
     * Determine whether or not access is granted to a specific controller/action.
     *
     * If the user has been authenticated, display the Access Forbidden error page.
     * Otherwise, give the user an opportunity to login before trying again.
     *
     * @return void
     */
    public function preDispatch()
    {
        $resource = null;
        if ($controller = $this->getActionController()) {
            if (isset($controller->aclResource)) {
                $resource = $controller->aclResource;
            }
        }

        if (!$resource && $this->_autoloadResourceObject) {
            $resource = $this->_getResourceObjectFromRequest();
        }

        if ($this->isAllowed($this->getRequest()->getActionName(), $resource)) {
            return;
        }

        if ($this->_currentUser) {
            $this->getRequest()->setControllerName('error')
                               ->setActionName('forbidden')
                               ->setModuleName('default')
                               ->setParams(array())
                               ->setDispatched(false);
        } else if (!$this->_isLoginRequest()) {
            $this->getRequest()->setControllerName('users')
                               ->setActionName('login')
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
     * @param Zend_Acl_Resource|string|null (Optional) Resource to check.
     * @see getResourceName()
     * @return boolean
     */
    public function isAllowed($privilege, $resource = null)
    {
        $allowed = $this->_allowed;
        if(isset($allowed[$privilege])) {
            return $allowed[$privilege];
        }

        if ($resource instanceof Zend_Acl_Resource_Interface) {
            $resourceObj = $resource;
            $resourceName = $resourceObj->getResourceId();
        } else if (is_string($resource)) {
            $resourceName = $resource;
        } else if (!$resource) {
            $resourceName = $this->getResourceName();
        }

        // Plugin writers do not need to define an ACL in order for their
        // controllers to work.
        if (!$this->_acl->has($resourceName)) {
            return true;
        }

        if (!isset($resourceObj)) {
            $resourceObj = $this->_acl->get($resourceName);
        }

        return $this->_acl->isAllowed($this->_currentUser, $resourceObj, $privilege);
    }

    /**
     * Retrieve the name of the ACL resource based on the name of the controller
     * and, if not the default module, the name of the module.
     *
     * @todo Should use the Zend inflection, though mine works better at the moment [KBK].
     * @return string
     */
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
     * @param User|null $currentUser
     */
    public function setCurrentUser($currentUser)
    {
        $this->_currentUser = $currentUser;
    }

    /**
     * Temporarily override the ACL's permissions for this controller
     *
     * @param string $rule
     * @param boolean $isAllowed
     */
    public function setAllowed($rule, $isAllowed = true)
    {
        $this->_allowed[$rule] = $isAllowed;
    }

    /**
     * Set whether the ACL helper should try to automatically load
     * a resource object from the request.
     *
     * @param boolean $autoload
     */
    public function setAutoloadResourceObject($autoload)
    {
        $this->_autoloadResourceObject = $autoload;
    }

    /**
     * Try to get the current resource object for the request.
     *
     * @return Zend_Acl_Resource_Interface|null
     */
    private function _getResourceObjectFromRequest()
    {
        try {
            $dbHelper = Zend_Controller_Action_HelperBroker::getExistingHelper('Db');
        } catch (Zend_Controller_Action_Exception $e) {
            return null;
        }

        try {
            $record = $dbHelper->findById();
        } catch (Omeka_Controller_Exception_404 $e) {
            return null;
        } catch (InvalidArgumentException $e) {
            return null;
        }

        if ($record instanceof Zend_Acl_Resource_Interface) {
            return $record;
        } else {
            return null;
        }
    }

    private function _isLoginRequest()
    {
        $request = $this->getRequest();
        return $request->getActionName() == 'login'
            && $request->getControllerName() == 'users';
    }
}
