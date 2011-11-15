<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
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
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract
{
    private $_requestMarkup;

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
        $config = Omeka_Context::getInstance()->config;
        
        $debugRequests = $config->debug->request;
        
        if ($debugRequests) {
            $router = Omeka_Context::getInstance()->getFrontController()
                                                  ->getRouter();
            $markup = $this->_getRequestMarkup($request, $router);
            $this->_requestMarkup = $markup;
        }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_requestMarkup) {
            $this->getResponse()->setBody($this->_requestMarkup);
        }
    }

    /**
     * Print database profiling info.
     *
     * Enabled conditionally when debug.profileDb = true in config.ini.
     *
     * @param Zend_Controller_Request_Abstract $request Request object.
     * @return void
     */
    public function dispatchLoopShutdown()
    {
        $enableProfiler = Omeka_Context::getInstance()->config->debug->profileDb;
        if (!$enableProfiler) {
            return;
        }
        $profiler = Omeka_Context::getInstance()->db->getProfiler();
        if ($profiler) {
            $markup = $this->_getProfilerMarkup($profiler);
            $this->getResponse()->setBody($markup);
        }
    }

    private function _getProfilerMarkup(Zend_Db_Profiler $profiler)
    {
        $totalTime    = $profiler->getTotalElapsedSecs();
        $queryCount   = $profiler->getTotalNumQueries();
        $longestTime  = 0;
        $longestQuery = null;
        $lines = array();
        $html = "<h2>Db Profiler</h2>\n";

        $lines[] = "The following queries were executed during the request:";
        $profiles = $profiler->getQueryProfiles();
        if (!$profiles) {
            return $html;
        }
        foreach ($profiles as $query) {
            $sql = $query->getQuery();
            $elapsedSecs = $query->getElapsedSecs();
            if ($elapsedSecs > $longestTime) {
              $longestTime  = $query->getElapsedSecs();
              $longestQuery = $sql;
            }
            $lines[] = "[$elapsedSecs] $sql";
        }

        $lines[] = 'Executed ' . $queryCount . ' queries in ' . $totalTime .
             ' seconds';
        $lines[] = 'Average query length: ' . $totalTime / $queryCount .
             ' seconds';
        $lines[] = 'Queries per second: ' . $queryCount / $totalTime;
        $lines[] = 'Longest query length: ' . $longestTime;
        $lines[] = "Longest query: \n" . $longestQuery;

        foreach ($lines as $line) {
            $html .= '<p>' . $line . '</p>' . "\n";
        }
        return $html;
    }
    
    /**
     * Create HTML markup for request debugging.
     * 
     * @param Zend_Controller_Request_Abstract $request Request object.
     * @param Zend_Controller_Router_Interface $router Router object.
     * @return string HTML markup.
     */
    private function _getRequestMarkup($request, $router)
    {
        $requestUri = $request->getRequestUri();
        
        $html = "<h2>Request Data</h2>\n\n<div>Request URI: <em>$requestUri</em>"
              . "</div>\n<div>Params:";
              
        $reqParams = $request->getParams();
        // Rendering the whole error_handler ArrayObject is annoying and causes
        // errors when request params are later used to assemble routes.
        if (array_key_exists('error_handler', $reqParams)) {
            $errHandler = $reqParams['error_handler'];
            $reqParams['exception'] = 
                (string)$errHandler['exception'];
            $reqParams['exception_type'] = $errHandler['type'];
            unset($reqParams['error_handler']);
        }
        $html .= '<pre>' . print_r($reqParams, true) . '</pre>';
        
        $html .= "</div>";
        
        if ($request->isPost()) {
            $html .= "<h2>Post Data</h2>";
            $html .= '<pre>' . print_r($_POST, true) . '</pre>';
        }
        
        $html .= "<h2>Session Data</h2>";
        $html .= '<pre>' . print_r($_SESSION, true) . '</pre>';

        $html .= "<h2>Server Data</h2>";
        $html .= '<pre>' . print_r($_SERVER, true) . '</pre>';
        
        $currentRoute = $router->getCurrentRouteName();
        $routes = $router->getRoutes();
        
        $html .= "<h2>Routing Data</h2>";
        $html .= "<div>Current Route: <strong>$currentRoute</strong></div>";
        $html .= "<div>Defined routes:\n\n";
        
        $html .= "<table><tr><th>Route Name</th><th>Matches Current Request</th><th>Assembled with current params</th></tr>";
        foreach ($routes as $routeName => $route) {
            try {
                $assembledRoute = $route->assemble($reqParams, true, true);
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

