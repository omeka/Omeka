<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Set up the router and the built-in routes.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Router extends Zend_Application_Resource_Router
{
    const DEFAULT_ROUTE_NAME = '/';
    const HOMEPAGE_ROUTE_NAME = 'navigation_homepage';
    
    /**
     * @return Zend_Controller_Router_Rewrite
     */
    public function init()
    {
        $router = parent::init();
        
        $front = $this->getBootstrap()->getResource('Frontcontroller');
        if ($front->getParam('api')) {
            // The API route is the only valid route for an API request.
            $router->addRoute('api', new Omeka_Controller_Router_Api);
        } else {
            $router->addConfig(new Zend_Config_Ini(CONFIG_DIR . '/routes.ini', 'routes'));
            fire_plugin_hook('define_routes', array('router' => $router));
            $this->_addHomepageRoute($router);
        }
        return $router;
    }
    
    /**
     * Adds the homepage route to the router (as specified by the navigation settings page)
     * The route will not be added if the user is currently on the admin theme.
     *
     * @param Zend_Controller_Router_Rewrite $router The router
     */
    private function _addHomepageRoute($router)
    {        
        // Don't add the route if the user is on the admin theme
        if (!is_admin_theme()) {            
            $homepageUri = get_option(Omeka_Form_Navigation::HOMEPAGE_URI_OPTION_NAME);
            $homepageUri = trim($homepageUri);
                                                
            $withoutAdminUri = $this->_leftTrim($this->_leftTrim($homepageUri, ADMIN_BASE_URL), '/' . ADMIN_WEB_DIR);
            if ($withoutAdminUri != $homepageUri) {
                // homepage uri is an admin link                
                $homepageUri = WEB_ROOT . '/' . ADMIN_WEB_DIR . $withoutAdminUri;                
                $this->addRedirectRouteForDefaultRoute(self::HOMEPAGE_ROUTE_NAME, $homepageUri, array(), $router);
            } else {
                // homepage uri is not an admin link
                
                // left trim root directory off of the homepage uri
                $homepageUri = $this->_leftTrim($homepageUri, PUBLIC_BASE_URL); 
                
                // make sure the new homepage is not the default homepage
                if ($homepageUri == '' || 
                    $homepageUri == self::DEFAULT_ROUTE_NAME || 
                    $homepageUri == PUBLIC_BASE_URL) {
                    return;
                }
                
                $homepageRequest = new Zend_Controller_Request_Http();
                $homepageRequest->setBaseUrl(WEB_ROOT); // web root includes server and root directory
                $homepageRequest->setRequestUri($homepageUri);
                $router->route($homepageRequest);
                $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
                if ($dispatcher->isDispatchable($homepageRequest)) {
                    // homepage is an internal link
                    $router->addRoute(
                         self::HOMEPAGE_ROUTE_NAME, 
                         new Zend_Controller_Router_Route(self::DEFAULT_ROUTE_NAME, $homepageRequest->getParams())
                    );
                } else {
                    // homepage is some external link or a broken internal link
                    $this->addRedirectRouteForDefaultRoute(self::HOMEPAGE_ROUTE_NAME, 
                                                          $homepageUri, 
                                                          array(), 
                                                          $router);
                }
            }
        }
    }
    
    /**
     * Adds a redirect route for the default route and returns whether the route was successfully added
     * If the current request matches the default route, then the flow will redirect to the 
     * index action of the RedirectorController, where the page will be redirected to the absolute uri
     * We must use this Redirector proxy controller because a user may be redirecting to an admin page and it needs
     * to reload the application from the admin context.  Also, the Zend router and dispatcher 
     * does not allow us to directly dispatch to an absolute uri. 
     *
     * @param String $routeName The name of the new redirect route
     * @param String $uri The absolute uri to redirect to the default route to
     * @param array $params The parameters for the redirect route.
     * @param Zend_Controller_Router_Rewrite $router The router
     * @return boolean Returns true if the route was successfully added, else false.
     */
    public function addRedirectRouteForDefaultRoute($routeName, $uri, $params = array(), $router=null) 
    {
        if ($router === null) {
            $router = $this;
        }
        
        $uri = trim($uri);
        if ($uri == '' || 
            $uri == self::DEFAULT_ROUTE_NAME || 
            $uri == PUBLIC_BASE_URL) {
            return false;
        }
        
        $router->addRoute(
             $routeName, 
             new Zend_Controller_Router_Route(self::DEFAULT_ROUTE_NAME, array_merge(array(
                'controller' => 'redirector',
                'action' => 'index',
                'redirect_uri' => $uri
             ), $params))
        );
        
        return true;
    }
    
    /**
     * Left trims the first occurrence of a string within a string. 
     * Note: it will only trim the first occurrence of the string.
     *
     * @param string $s  The base string 
     * @param string $n The string to remove from the left side of the base string
     * @return string
     */
    protected function _leftTrim($s, $n) 
    {
        if ($n == '') {
            return $s;
        }
        $pos = strpos($s, $n);
        if ($pos === FALSE || $pos !== 0) {
            return $s;
        }
        return substr($s, strlen($n));
    }
}
