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
class Omeka_Core_Resource_Debug extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Config');
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
        $config = $bootstrap->getResource('Config');
        
        // Uncaught exceptions should bubble up to the browser level since we
        // are essentially in debug/install mode. Otherwise, we should make use
        // of the ErrorController, which WILL NOT LOAD IF YOU ENABLE EXCEPTIONS
        // (took me awhile to figure this out).
        if ((boolean)$config->debug->exceptions) {
            $front->throwExceptions(true);  
        }
        
        // This plugin allows for debugging request objects without inserting 
        // debugging code into the Zend Framework code files.        
        $front->registerPlugin(new Omeka_Controller_Plugin_Debug);
    }
}
