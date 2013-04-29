<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller\Plugin
 */
class Omeka_Controller_Plugin_Api extends Zend_Controller_Plugin_Abstract
{
    /**
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        
        // Check for an API request.
        if (!$front->getParam('api')) {
            return;
        }
        
        // Throw an error if a key was given but there is no user identity.
        if (isset($_GET['key']) && !Zend_Auth::getInstance()->hasIdentity()) {
            throw new Omeka_Controller_Exception_403('Invalid key.');
        }
        
        // Set the API controller directories.
        $apiControllerDirectories = array();
        $controllerDirectories = $front->getControllerDirectory();
        foreach ($controllerDirectories as $module => $controllerDirectory) {
            $apiControllerDirectories[$module] = "$controllerDirectory/api";
        }
        $front->setControllerDirectory($apiControllerDirectories);
    }
}

