<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Set up the router and the built-in routes.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Application_Resource_Router extends Zend_Application_Resource_Router
{
    /**
     * @return Zend_Controller_Router_Rewrite
     */
    public function init()
    {
        $router = parent::init();
        $routesIni = new Zend_Config_Ini(CONFIG_DIR . '/' . 'routes.ini', 'routes');
        $router->addConfig($routesIni);
        // Plugins hook into this.
        fire_plugin_hook('define_routes', array('router' => $router));
        
        $this->_addHomepageRoute($router);
        
        return $router;
    }
    
    private function _addHomepageRoute($router)
    {
        $homepageUri = get_option(Omeka_Form_Navigation::HOMEPAGE_URI_OPTION_NAME);
        if (!is_admin_theme()) {
                        
            $homepageRequest = new Zend_Controller_Request_Http();
            $homepageRequest->setBaseUrl(WEB_ROOT);
            $homepageRequest->setRequestUri($homepageUri);
            $router->route($homepageRequest);
            
            $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
            if ($dispatcher->isDispatchable($homepageRequest)) {
                $router->addRoute(
                     'navigation_homepage', 
                     new Zend_Controller_Router_Route(
                         '/', 
                         $homepageRequest->getParams()
                     )
                 );
            }
        }
    }
}
