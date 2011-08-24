<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * This controller plugin allows for all functionality that is specific to the Admin theme.
 *
 * For now, all this includes is preventing unauthenticated access to all admin pages, 
 * with the exception of a few white-listed URLs, which are stored in this plugin.
 *
 * This controller plugin should be loaded only in the admin bootstrap.  
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Plugin_Admin extends Zend_Controller_Plugin_Abstract
{
    /**
     * Controller/Action list for admin actions that do not require being logged-in
     *
     * @var string
     */
    protected $_adminWhitelist = array(
        array('controller' => 'users', 'action' => 'activate'),
        array('controller' => 'users', 'action' => 'login'),
        array('controller' => 'users', 'action' => 'forgot-password'),
        array('controller' => 'installer', 'action' => 'notify'),
        array('controller' => 'error', 'action' => 'error')
    );
    
    /**
     * Direct requests to the admin interface.
     * Called upon router startup, before the request is routed.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // Let the request know that we want to go through the admin interface.
        $request->setParam('admin', true);
    }
    
    /**
     * Require login when attempting to access the admin interface.
     * Whitelisted controller/action combinations are exempt from this
     * requirement.
     * Called before dispatching.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_adminWhitelist = apply_filters('admin_whitelist', $this->_adminWhitelist);

        if ($this->_requireLogin($request)) {
        
            // Deal with the login stuff
            require_once 'Zend/Auth.php';
            require_once 'Zend/Session.php';

            if (!($auth = $this->getAuth())) {
                throw new Exception('Auth object must be available when routing admin requests!');
            }
        
            if (!$auth->hasIdentity()) {
                // capture the intended controller / action for the redirect
                $session = new Zend_Session_Namespace;
                $session->redirect = $request->getPathInfo() . 
                (!empty($_GET) ? '?' . http_build_query($_GET) : '');
            
                // finally, send to a login page
                $this->getRedirector()->goto('login', 'users', 'default');
            }
        }
    }
    
    /**
     * Return the redirector action helper.
     *
     * @return Zend_Controller_Action_Helper_Redirector
     */
    public function getRedirector()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
    }
    
    /**
     * Return the auth object.
     *
     * @uses Omeka_Context
     * @return Zend_Auth
     */
    public function getAuth()
    {
        return Omeka_Context::getInstance()->getAuth();
    }

    /**
     * Determine whether or not the request requires an authenticated 
     * user.  
     *
     * @return boolean
     */
    private function _requireLogin($request)
    {
        $action = $request->getActionName();
        $controller = $request->getControllerName();
        $module = $request->getModuleName();

        foreach ($this->_adminWhitelist as $entry) {
            // Any whitelist entry that omits the module will be assumed to be
            // talking about the default module.
            if (!array_key_exists('module', $entry)) {
                $entry['module'] = 'default';
            }

            $inWhitelist = ($entry['controller'] == $controller) 
                        && ($entry['action'] == $action);

            // Module name is not always defined in the request.
            if ($module !== null) {
                $inWhitelist = $inWhitelist && ($entry['module'] == $module);
            }

            if ($inWhitelist) {
                return false;
            }
        }

        return true;
    }
}

