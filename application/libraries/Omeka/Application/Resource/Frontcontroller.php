<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Front controller resource.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller
{
    /**
     * @return Zend_Controller_Front
     */
    public function init()
    {           
        // If 'skipOmekaMvc' is set on the front controller, skip the Omeka 
        // custom behavior here, and stick with vanilla Zend. Because of 
        // resource naming conflicts, i.e. both Zend and Omeka resource plugins 
        // called 'Frontcontroller', there is no easy way to use the default 
        // Zend resource instead of Omeka's.  Situations where this would be 
        // useful include installation of Omeka, or in any future modules that 
        // want to bypass the dependency graph of Omeka in favor of using the 
        // (relatively) simpler Zend Framework defaults.
        $front = parent::init();
        
        if ($front->getParam('skipOmekaMvc')) {
            return $front;
        }
        
        // REST API requests require a slightly different controller environment. 
        // They must be made from the public side and the URL must match a 
        // particular pattern.
        $request = new Zend_Controller_Request_Http;
        if (!$front->getParam('admin') 
            && preg_match('#^/api/([a-z_]+)(.+)?$#', $request->getPathInfo())
        ) {
            // Flag this as an API request.
            $front->setParam('api', true);
            // Displaying errors will break client parsers, so hide them.
            ini_set('display_errors', 0);
            // Register API-specific controller logic.
            $front->registerPlugin(new Omeka_Controller_Plugin_Api);
        }
        
        // Admin requests require a sligntly different controller environment.
        if ($front->getParam('admin')) {
            // Register admin-specific controller logic.
            $front->registerPlugin(new Omeka_Controller_Plugin_Admin);
        }
        
        // Plugin broker is required to set plugin-defined response contexts
        $bootstrap = $this->getBootstrap();
        if ($bootstrap->hasPluginResource('PluginBroker')) {
            $bootstrap->bootstrap('PluginBroker');
        }
        
        // Action helpers
        $this->getBootstrap()->bootstrap('Helpers');
        
        if ($sslConfig = $this->getBootstrap()->config->ssl) {
            $redirector = 
                Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $auth = $this->getBootstrap()->bootstrap('Auth')->auth;
            $front->registerPlugin(
                new Omeka_Controller_Plugin_Ssl((string)$sslConfig, $redirector, $auth));
        }

        // Add a default content-type fallback.
        $front->registerPlugin(new Omeka_Controller_Plugin_DefaultContentType);
        
        return $front;
    }
}
