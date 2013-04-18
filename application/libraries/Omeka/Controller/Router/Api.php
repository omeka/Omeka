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
    const DEFAULT_CONTROLLER = 'api';
    
    /**
     * @var All controller actions that are legal for the API.
     */
    protected $_legalActions = array('index', 'get', 'post', 'put', 'delete');
    
    /**
     * @var The default API resources and their routing information.
     * 
     * Use the "api_resources" filter to add resources, following this format:
     * 
     * <code>
     * // For the path: /api/your_resources/:id
     * 'your_resources' => array(
     *     // Controller associated with your resource.
     *     'controller' => 'your-resource-controller',
     *     // Record associated with your resource.
     *     'record' => 'YourResourceRecord',
     *     // List of actions available for your resource.
     *     'actions' => array(
     *         'index',  // GET request without ID
     *         'get',    // GET request with ID
     *         'post',   // POST request
     *         'put',    // PUT request (ID is required)
     *         'delete', // DELETE request (ID is required)
     *     ), 
     * )
     * </code>
     * 
     * If not given, controller falls back to the default controller (api). 
     * Resources using the default controller MUST include a record. Remove 
     * actions if they are not wanted or not implemented.
     */
    protected $_apiResources = array(
        'resources' => array(
            'controller' => 'resources', 
            'actions' => array('index')
        ), 
        'collections' => array(
            'record' => 'Collection', 
            'actions' => array('index', 'get')
        ), 
        'items' => array(
            'record' => 'Item', 
            'actions' => array('index', 'get')
        ), 
        'files' => array(
            'record' => 'File', 
            'actions' => array('index', 'get')
        ), 
    );
    
    public static function getInstance(Zend_Config $config)
    {
        return new self;
    }
    
    /**
     * Match the user submitted path.
     * 
     * @throws Zend_Controller_Router_Exception
     * @param Zend_Controller_Request_Http $request
     * @return array|false
     */
    public function match($request)
    {
        // Extract URL components.
        preg_match('#^/api/([a-z_]+)(.+)?$#', $request->getPathInfo(), $matches);
        
        if (!$matches) {
            if (0 === strpos($request->getPathInfo(), '/api')) {
                throw new Zend_Controller_Router_Exception('Invalid resource');
            }
            return false;
        }
        
        $resource = $matches[1];
        $params = array();
        if (isset($matches[2]) && '/' != $matches[2]) {
            $params = explode('/', $matches[2]);
            array_shift($params);
        }
        
        // Get all available API resources.
        $apiResources = apply_filters('api_resources', $this->_apiResources);
        
        // Get and validate resource, record, controller, and action.
        $resource = $this->_getResource($resource, $apiResources);
        if (false === $resource) {
            throw new Zend_Controller_Router_Exception('Invalid resource');
        }
        $record = $this->_getRecord($resource, $apiResources);
        if (false === $record) {
            throw new Zend_Controller_Router_Exception('Invalid record');
        }
        $controller = $this->_getController($resource, $apiResources);
        if (false === $controller) {
            throw new Zend_Controller_Router_Exception('Invalid controller');
        }
        $action = $this->_getAction($request->getMethod(), $params, $resource, $apiResources);
        if (false === $action) {
            throw new Zend_Controller_Router_Exception('Invalid action');
        }
        
        // Set the route variables. Namespace the API params to prevent 
        // collisions with GET.
        $routeVars = array(
            'controller'   => $controller, 
            'action'       => $action, 
            'api_resource' => $resource, 
            'api_record'   => $record, 
            'api_params'   => $params, 
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
     * Return this route's record.
     * 
     * @param string $resource
     * @param array $apiResources
     * @return string|null|false
     */
    protected function _getRecord($resource, array $apiResources)
    {
        // Resources using the default controller must pass a record.
        if (!isset($apiResources[$resource]['controller']) 
            && !isset($apiResources[$resource]['record'])) {
            return false;
        }
        if (isset($apiResources[$resource]['record'])) {
            return $apiResources[$resource]['record'];
        }
        return null;
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
        
        // The action must available for the record type.
        $legalActions = $this->_legalActions;
        if (isset($apiResources[$resource]['actions'])) {
            $legalActions = $apiResources[$resource]['actions'];
        }
        if (!in_array($action, $legalActions)) {
            return false;
        }
        
        return $action;
    }
}
