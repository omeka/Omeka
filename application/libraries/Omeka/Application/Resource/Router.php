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
        if (is_admin_theme()) {
            return;
        }

        $homepageUri = trim(get_option(Omeka_Form_Navigation::HOMEPAGE_URI_OPTION_NAME));

        if (strpos($homepageUri, ADMIN_BASE_URL) === 0) {
            // homepage uri is an admin link
            $this->_addHomepageRedirect($homepageUri, $router);
        } else if (strpos($homepageUri, '?') === false) {
            // left trim root directory off of the homepage uri
            $relativeUri = $this->_leftTrim($homepageUri, PUBLIC_BASE_URL);

            // make sure the new homepage is not the default homepage
            if ($relativeUri == '' || $relativeUri == '/') {
                return;
            }

            $homepageRequest = new Zend_Controller_Request_Http();
            $homepageRequest->setRequestUri($homepageUri);
            $router->route($homepageRequest);
            $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
            if ($dispatcher->isDispatchable($homepageRequest)) {
                // homepage is an internal link
                $router->addRoute(
                     self::HOMEPAGE_ROUTE_NAME,
                     new Zend_Controller_Router_Route('/', $homepageRequest->getParams())
                );
                return;
            }
        }
        // homepage is some external link, a broken internal link, or has a
        // query string
        $this->_addHomepageRedirect($homepageUri, $router);
    }
    
    /**
     * Adds a redirect route for the homepage.
     *
     * A redirect is required to make a "homepage" that is an external URL, an
     * admin URL, or a URL with a query string.
     *
     * @param string $uri The absolute uri to redirect to the default route to
     * @param Zend_Controller_Router_Rewrite $router The router
     * @return boolean True if the route was successfully added, else false.
     */
    protected function _addHomepageRedirect($uri, $router)
    {
        // Handle possible internal links by stripping the base URL
        $uri = $this->_leftTrim($uri, PUBLIC_BASE_URL);

        if ($uri == '' || $uri == '/'
            || strpos($uri, '?') === 0 || strpos($uri, '/?') === 0) {
            return false;
        }
        
        $router->addRoute(
             self::HOMEPAGE_ROUTE_NAME,
             new Zend_Controller_Router_Route('/', array(
                'controller' => 'redirector',
                'action' => 'index',
                'redirect_uri' => $uri
             ))
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
        if ($pos !== 0) {
            return $s;
        }
        return substr($s, strlen($n));
    }
}
