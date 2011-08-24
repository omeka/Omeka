<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Front controller resource.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller
{
    /**
     * @return Zend_Controller_Front
     */
    public function init()
    {           
        // If 'skipOmekaMvc' is set on the front controller, skip the
        // Omeka custom behavior here, and stick with vanilla Zend.
        // Because of resource naming conflicts, i.e. both Zend and Omeka 
        // resource plugins called 'Frontcontroller', there is no easy way to
        // use the default Zend resource instead of Omeka's.  Situations where
        // this would be useful include installation of Omeka, or in any future
        // modules that want to bypass the dependency graph of Omeka in favor
        // of using the (relatively) simpler Zend Framework defaults.
        $front = parent::init();

        if ($front->getParam('skipOmekaMvc')) {
            return $front;
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
                new Omeka_Controller_Plugin_Ssl((string)$sslConfig,
                                                $redirector,
                                                $auth));
        }
        
        return $front;
    }

    /**
     * Convenience method for backwards-compatibility with Omeka 1.x.
     */
    public static function getDefaultResponseContexts()
    {
        return Omeka_Core_Resource_Helpers::getDefaultResponseContexts();
    }
}
