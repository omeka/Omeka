<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Sets up debugging output for web requests (if enabled).
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Debug extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return void
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
                
        // This plugin allows for debugging request objects without inserting 
        // debugging code into the Zend Framework code files.        
        $front->registerPlugin(new Omeka_Controller_Plugin_Debug);
    }
}
