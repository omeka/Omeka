<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * This controller plugin allows for all functionality that is specific to the Admin theme.
 *
 * For now, all this includes is preventing unauthenticated access to all admin pages, 
 * with the exception of a few white-listed URLs, which are stored in this plugin.
 *
 * This controller plugin should be loaded only in the admin bootstrap.  
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Controller_Plugin_Admin extends Zend_Controller_Plugin_Abstract
{
    /**
     * Controller/Action list for admin actions that do not require being logged-in
     *
     * @var string
     **/
    protected $_adminWhitelist = array(array('controller' => 'users', 'action' => 'activate'), 
                                       array('controller' =>'users', 'action' => 'login'),
                                       array('controller' => 'users', 'action' => 'forgot-password'),
                                       array('controller' => 'installer', 'action' => 'notify'));
    
    /**
    *
    * @param Zend_Controller_Request_Abstract $request
    * @return void
    */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // Let the request know that we want to go through the admin interface.
        $request->setParam('admin', true);
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $action = $request->getActionName();
        $controller = $request->getControllerName();
        
        $overrideLogin = false;

        // if ($request->getParam('admin')) {
            foreach ($this->_adminWhitelist as $entry) {
                if (($entry['controller'] == $controller) && ($entry['action'] == $action)) {
                    $overrideLogin = true;
                    break;
                }
            }
            
            // If we haven't overridden the need to login
            if (!$overrideLogin) {
            
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
        // }
    }
    
    public function getRedirector()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
    }
    
    public function getAuth()
    {
        return Omeka_Context::getInstance()->getAuth();
    }
    
}