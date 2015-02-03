<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Router for the Omeka API.
 * 
 * @package Omeka\Controller\Router
 */
class Omeka_Controller_Router_Api extends Zend_Controller_Router_Route_Abstract
{
    /**
     * The default controller name.
     */
    const DEFAULT_MODULE = 'default';
    
    /**
     * The default controller name.
     */
    const DEFAULT_CONTROLLER = 'api';
    
    /**
     * @var All controller actions that are legal for the API.
     */
    protected $_legalActions = array('index', 'get', 'post', 'put', 'delete');
    
    /**
     * @var GET parameters that are legal for all actions.
     */
    protected $_legalParams = array('key', 'callback', 'pretty_print');
    
    /**
     * @var GET parameters that are legal for index actions.
     */
    protected $_legalIndexParams = array('page', 'per_page', 'sort_field', 'sort_dir');
    
    public static function getInstance(Zend_Config $config)
    {
        return new self;
    }
    
    /**
     * Match the user submitted path.
     * 
     * Via Omeka_Application_Resource_Router, this is the only available route 
     * for API requests.
     * 
     * @throws Omeka_Controller_Exception_Api
     * @param Zend_Controller_Request_Http $request
     * @return array|false
     */
    public function match($request)
    {
        $front = Zend_Controller_Front::getInstance();
        
        // Extract URL components.
        preg_match('#^/api/([a-z_]+)(.+)?$#', $request->getPathInfo(), $matches);
        
        if (!$matches) {
            return false;
        }
        
        // Throw an error if a key was given but there is no user identity.
        if (isset($_GET['key']) && !Zend_Auth::getInstance()->hasIdentity()) {
            throw new Omeka_Controller_Exception_Api('Invalid key.', 403);
        }
        
        // The API must be enabled.
        if (!get_option('api_enable')) {
            throw new Omeka_Controller_Exception_Api('API is disabled', 403);
        }
        
        $resource = $matches[1];
        
        // Extract path parameters. Not to be confused with request parameters.
        $params = array();
        if (isset($matches[2]) && '/' != $matches[2]) {
            $params = explode('/', $matches[2]);
            array_shift($params);
        }
        
        // Allow clients to override the HTTP method. This is helpful if the 
        // server is configured to reject certain methods.
        if (!$method = $request->getHeader('X-HTTP-Method-Override')) {
            $method = $request->getMethod();
        }
        
        // Get all available API resources.
        $apiResources = $front->getParam('api_resources');
        
        // Get and validate resource, record_type, module, controller, and action.
        $resource   = $this->_getResource($resource, $apiResources);
        $recordType = $this->_getRecordType($resource, $apiResources);
        $module     = $this->_getModule($resource, $apiResources);
        $controller = $this->_getController($resource, $apiResources);
        $action     = $this->_getAction($method, $params, $resource, $apiResources);
        
        // Validate the GET parameters.
        $this->_validateParams($action, $resource, $apiResources);
        
        // Set the route variables. Namespace the API parameters to prevent 
        // collisions with the request parameters.
        $routeVars = array(
            'module'          => $module, 
            'controller'      => $controller, 
            'action'          => $action, 
            'api_resource'    => $resource, 
            'api_record_type' => $recordType, 
            'api_params'      => $params, 
        );
        
        return $routeVars;
    }
    
    public function assemble($data = array(), $reset = false, $encode = false)
    {}
    
    /**
     * Return this route's resource.
     * 
     * @throws Omeka_Controller_Exception_Api
     * @param string $resource
     * @param array $apiResources
     * @return string
     */
    protected function _getResource($resource, array $apiResources)
    {
        if (!array_key_exists($resource, $apiResources)) {
            throw new Omeka_Controller_Exception_Api("The \"$resource\" resource is unavailable.", 404);
        }
        return $resource;
    }
    
    /**
     * Return this route's record type.
     * 
     * @throws Omeka_Controller_Exception_Api
     * @param string $resource
     * @param array $apiResources
     * @return string|null
     */
    protected function _getRecordType($resource, array $apiResources)
    {
        // Resources using the default controller must register a record type.
        if (!isset($apiResources[$resource]['controller']) 
            && !isset($apiResources[$resource]['record_type'])
        ) {
            throw new Omeka_Controller_Exception_Api('Resources using the default controller must register a record type.', 500);
        }
        if (isset($apiResources[$resource]['record_type'])) {
            return $apiResources[$resource]['record_type'];
        }
        return null;
    }
    
    /**
     * Return this route's module.
     * 
     * @param string $resource
     * @param array $apiResources
     * @return string
     */
    protected function _getModule($resource, array $apiResources)
    {
        if (isset($apiResources[$resource]['module'])) {
            return $apiResources[$resource]['module'];
        }
        return self::DEFAULT_MODULE;
    }
    
    /**
     * Return this route's controller.
     * 
     * @param string $resource
     * @param array $apiResources
     * @return string
     */
    protected function _getController($resource, array $apiResources)
    {
        if (isset($apiResources[$resource]['controller'])) {
            return $apiResources[$resource]['controller'];
        }
        return self::DEFAULT_CONTROLLER;
    }
    
    /**
     * Return this route's action.
     * 
     * @throws Omeka_Controller_Exception_Api
     * @param string $method
     * @param array $params
     * @param string $resource
     * @param array $apiResources
     * @return string
     */
    protected function _getAction($method, array $params, $resource, array $apiResources)
    {
        // Get the action.
        $action = strtolower($method);
        if ('get' == $action) {
            $action = $params ? 'get' : 'index';
        }
        
        if ($params && 'post' == $action) {
            throw new Omeka_Controller_Exception_Api('POST requests must not include an ID.', 405);
        }
        
        if (!$params && in_array($action, array('put', 'delete'))) {
            throw new Omeka_Controller_Exception_Api('PUT and DELETE requests must include an ID.', 405);
        }
        
        // The action must be available for the resource.
        $legalActions = $this->_legalActions;
        if (isset($apiResources[$resource]['actions'])) {
            $legalActions = $apiResources[$resource]['actions'];
        }
        if (!in_array($action, $legalActions)) {
            throw new Omeka_Controller_Exception_Api("This resource does not implement the \"$action\" action.", 405);
        }
        
        return $action;
    }
    
    /**
     * Validate the GET parameters against the whitelist.
     * 
     * @throws Omeka_Controller_Exception_Api
     * @param string $action
     * @param string $resource
     * @param array $apiResources
     */
    protected function _validateParams($action, $resource, $apiResources)
    {
        $legalParams = $this->_legalParams;
        
        // The index action may allow more GET parameters.
        if ('index' == $action) {
            $legalParams = array_merge($legalParams, $this->_legalIndexParams);
            if (isset($apiResources[$resource]['index_params'])) {
                $legalParams = array_merge($legalParams, $apiResources[$resource]['index_params']);
            }
            $legalParams = array_unique($legalParams);
        }
        
        foreach ($_GET as $key => $value) {
            if (!in_array($key, $legalParams)) {
                throw new Omeka_Controller_Exception_Api("Invalid GET request parameter: \"$key\"", 400);
            }
        }
    }
}
