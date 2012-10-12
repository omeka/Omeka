<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Sets up debugging output for web requests (if enabled).
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Debug extends Zend_Application_Resource_ResourceAbstract
{
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
