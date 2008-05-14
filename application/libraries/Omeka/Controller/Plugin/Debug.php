<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * This controller plugin allows for debugging Request objects
 * without inserting debugging code into the Zend Framework
 * code files.
 *
 * Debugging web requests is enabled by setting 'debug.request = true'
 * in the config.ini file.
 * 
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $context = Omeka_Context::getInstance();
        $config = $context->getConfig('basic');
        
        $debugRequests = $config->debug->request;
        
        if($debugRequests) {
/*             $route = $context->getFrontController()->getRouter()->getCurrentRoute();
            
            Zend_Debug::dump( $route );exit; */
            
            Zend_Debug::dump( $request );exit;
        }
    }
}

