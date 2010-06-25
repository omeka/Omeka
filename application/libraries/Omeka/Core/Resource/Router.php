<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Set up the router and the built-in routes.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Core_Resource_Router extends Zend_Application_Resource_Router
{       
    public function init()
    {
        $router = parent::init();
        $routesIni = new Zend_Config_Ini(CONFIG_DIR . DIRECTORY_SEPARATOR . 'routes.ini', 'routes');
        $router->addConfig($routesIni);
        // Plugins hook into this.
        fire_plugin_hook('define_routes', $router);
        return $router;
    }
}
