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
    protected $_legalParams = array('key', 'callback');
    
    /**
     * @var GET parameters that are legal for index actions.
     */
    protected $_legalIndexParams = array('page');
    
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
     * @throws Omeka_Controller_Exception_403
     * @throws Omeka_Controller_Exception_404
     * @param Zend_Controller_Request_Http $request
     * @return array|false
     */
    public function match($request)
    {
        $front = Zend_Controller_Front::getInstance();
        
        // Extract URL components.
        preg_match('#^/api/([a-z_]+)(.+)?$#', $request->getPathInfo(), $matches);
        
        if (!$matches) {
            if (0 === strpos($request->getPathInfo(), '/api')) {
                throw new Omeka_Controller_Exception_404('Invalid resource');
            }
            return false;
        }
        
        // The API must be enabled.
        if (!get_option('api_enable')) {
            throw new Omeka_Controller_Exception_403('API is disabled');
        }
        
        $resource = $matches[1];
        
        // Extract path parameters. Not to be confused with request parameters.
        $params = array();
        if (isset($matches[2]) && '/' != $matches[2]) {
            $params = explode('/', $matches[2]);
            array_shift($params);
        }
        
        // Get all available API resources.
        $apiResources = $front->getParam('api_resources');
        
        // Get and validate resource, record_type, module, controller, and action.
        $resource = $this->_getResource($resource, $apiResources);
        if (false === $resource) {
            throw new Omeka_Controller_Exception_404('Invalid resource');
        }
        $recordType = $this->_getRecordType($resource, $apiResources);
        if (false === $recordType) {
            throw new Omeka_Controller_Exception_404('Invalid record type');
        }
        $module = $this->_getModule($resource, $apiResources);
        if (false === $module) {
            throw new Omeka_Controller_Exception_404('Invalid module');
        }
        $controller = $this->_getController($resource, $apiResources);
        if (false === $controller) {
            throw new Omeka_Controller_Exception_404('Invalid controller');
        }
        $action = $this->_getAction($request->getMethod(), $params, $resource, $apiResources);
        if (false === $action) {
            throw new Omeka_Controller_Exception_404('Invalid action');
        }
        
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
     * @param string $resource
     * @param array $apiResources
     * @return string|false
     */
    protected function _getResource($resource, array $apiResources)
    {
        if (!array_key_exists($resource, $apiResources)) {
            return false;
        }
        return $resource;
    }
    
    /**
     * Return this route's record type.
     * 
     * @param string $resource
     * @param array $apiResources
     * @return string|null|false
     */
    protected function _getRecordType($resource, array $apiResources)
    {
        // Resources using the default controller must pass a record type.
        if (!isset($apiResources[$resource]['controller']) 
            && !isset($apiResources[$resource]['record_type'])) {
            return false;
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
     * @param string $method
     * @param array $params
     * @param string $resource
     * @param array $apiResources
     * @return string|false
     */
    protected function _getAction($method, array $params, $resource, array $apiResources)
    {
        // Get the action.
        $action = strtolower($method);
        if ('get' == $action) {
            $action = $params ? 'get' : 'index';
        }
        
        // PUT and DELETE methods require parameters.
        if (!$params && in_array($action, array('put', 'delete'))) {
            return false;
        }
        
        // The action must be available for the resource.
        $legalActions = $this->_legalActions;
        if (isset($apiResources[$resource]['actions'])) {
            $legalActions = $apiResources[$resource]['actions'];
        }
        if (!in_array($action, $legalActions)) {
            return false;
        }
        
        return $action;
    }
    
    /**
     * Validate the GET parameters against the whitelist.
     * 
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
                throw new Omeka_Controller_Exception_404("Invalid GET parameter: \"$key\"");
            }
        }
    }
}
