<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller
{
    public function init()
    {           
        $front = Zend_Controller_Front::getInstance();
        // Because of resource naming conflicts, i.e. both Zend and Omeka 
        // resource plugins called 'Frontcontroller', there is no easy way to
        // use the default Zend resource instead of Omeka's.  Situations where
        // this would be useful include installation of Omeka, or in any future
        // modules that want to bypass the dependency graph of Omeka in favor
        // of using the (relatively) simpler Zend Framework defaults.
        if ($front->getParam('skipOmekaMvc')) {
            return parent::init();
        }
        
        // Plugin broker is required to set plugin-defined response contexts
        $bootstrap = $this->getBootstrap();
        if ($bootstrap->hasPluginResource('PluginBroker')) {
            $bootstrap->bootstrap('PluginBroker');
        }
        
        // Front controller
        $front->addControllerDirectory(CONTROLLER_DIR, 'default');
                                                        
        // Action helpers
        $this->getBootstrap()->bootstrap('Helpers');

        // Register the JSOND controller plugin
        $front->registerPlugin(new Omeka_Controller_Plugin_Jsonp);
        
        // Register the Upgrade controller plugin
        $front->registerPlugin(new Omeka_Controller_Plugin_Upgrade);
        
        // Register the HtmlPurifier controller plugin
        $bootstrap->bootstrap('Htmlpurifier');
        if ($htmlPurifier = $bootstrap->getResource('Htmlpurifier')) {
            $front->registerPlugin(new Omeka_Controller_Plugin_HtmlPurifier($htmlPurifier));
        }
        
        return $front;
    }     
}
