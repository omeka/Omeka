<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Authentication resource.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Auth extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Zend_Auth
     */
    public function init()
    {
        // Make sure the session has been configured properly beforehand in order
        // to avoid bypassing Zend_Session::start()'s configuration mechanism.
        $this->getBootstrap()->bootstrap('Session');
        
        return Zend_Auth::getInstance();
    }
}
