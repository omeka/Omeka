<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * This controller plugin allows for debugging Request objects
 * without inserting debugging code into the Zend Framework
 * code files.
 *
 * Debugging web requests is enabled by setting 'debug.request = true'
 * in the config.ini file.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
    /**
     * Print request debugging info for every request.
     *
     * Has no effect if request debugging is not enabled in config.ini.
     *
     * @param Zend_Controller_Request_Abstract $request Request object.
     * @return void
     */
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
    
    /**
     * Create HTML markup for request debugging.
     * 
     * @param Zend_Controller_Request_Abstract $request Request object.
     * @param Zend_Controller_Router_Interface $router Router object.
     * @return string HTML markup.
     */
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
        $html .= "<div>Defined routes:\n\n";
        
        $html .= "<table><tr><th>Route Name</th><th>Matches Current Request</th><th>Assembled with current params</th></tr>";
        foreach ($routes as $routeName => $route) {
            try {
                $assembledRoute = $route->assemble($request->getParams(), true, true);
            } catch (Exception $e) {
                $assembledRoute = "Could not assemble: " . $e->getMessage();
            }
            if ($route instanceof Zend_Controller_Router_Route_Chain) {
                $routeIsMatched = $route->match($request);
            } else {
                $routeIsMatched = $route->match($request->getPathInfo());
            }
            
            $html .= "<tr><td>$routeName</td><td>" . ($routeIsMatched ? 'true' : 'false') . "</td><td>$assembledRoute</td></tr>";
        }
                
        $html .= "</table>";
        
        $html .= "<h2>Cookie Data</h2>";
        $html .= '<pre>' . print_r($_COOKIE, true) . '</pre>';
        
        
        return $html;
    }        
}

