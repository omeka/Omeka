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
        
        if ($debugRequests) {
            $router = $context->getFrontController()->getRouter();
            echo $this->_getMarkup($request, $router);exit;
        }
    }
    
    private function _getMarkup($request, $router)
    {
        $requestUri = $request->getRequestUri();
        
        $html = "<h2>Request Data</h2>\n\n<div>Request URI: <em>$requestUri</em>"
              . "</div>\n<div>Params:";
              
        $html .= '<pre>' . print_r($request->getParams(), true) . '</pre>';
        
        $html .= "</div>";
        
        if ($request->isPost()) {
            $html .= "<h2>Post Data</h2>";
            $html .= '<pre>' . print_r($_POST, true) . '</pre>';
        }
        
        $html .= "<h2>Session Data</h2>";
        $html .= '<pre>' . print_r($_SESSION, true) . '</pre>';
        
        $currentRoute = $router->getCurrentRouteName();
        $routes = $router->getRoutes();
        
        $html .= "<h2>Routing Data</h2>";
        $html .= "<div>Current Route: <strong>$currentRoute</strong></div>";
        $html .= "<div>Defined routes:<ul>";
        
        foreach ($routes as $routeName => $route) {
            $html .= "<li>" . $routeName . "</li>";
        }
                
        $html .= "</ul>";
        
        $html .= "<h2>Cookie Data</h2>";
        $html .= '<pre>' . print_r($_COOKIE, true) . '</pre>';
        
        
        return $html;
    }        
}

