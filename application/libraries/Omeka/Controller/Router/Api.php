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
     *     // Type of record associated with your resource.
     *     'record_type' => 'YourResourceRecord',
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
     * If not given, "controller" falls back to the default controller (api). 
     * Resources using the default controller MUST include a "record_type". 
     * Remove "actions" that are not wanted or not implemented.
     */
    protected static $_apiResources = array(
        'resources' => array(
            'controller' => 'resources', 
            'actions' => array('index')
        ), 
        'collections' => array(
            'record_type' => 'Collection', 
            'actions' => array('index', 'get')
        ), 
        'items' => array(
            'record_type' => 'Item', 
            'actions' => array('index', 'get')
        ), 
        'files' => array(
            'record_type' => 'File', 
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
     * @throws Omeka_Controller_Exception_403
     * @throws Omeka_Controller_Exception_404
     * @param Zend_Controller_Request_Http $request
     * @return array|false
     */
    public function match($request)
    {
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
        $params = array();
        if (isset($matches[2]) && '/' != $matches[2]) {
            $params = explode('/', $matches[2]);
            array_shift($params);
        }
        
        // Get all available API resources.
        $apiResources = self::getApiResources();
        
        // Get and validate resource, record_type, controller, and action.
        $resource = $this->_getResource($resource, $apiResources);
        if (false === $resource) {
            throw new Omeka_Controller_Exception_404('Invalid resource');
        }
        $recordType = $this->_getRecordType($resource, $apiResources);
        if (false === $recordType) {
            throw new Omeka_Controller_Exception_404('Invalid record type');
        }
        $controller = $this->_getController($resource, $apiResources);
        if (false === $controller) {
            throw new Omeka_Controller_Exception_404('Invalid controller');
        }
        $action = $this->_getAction($request->getMethod(), $params, $resource, $apiResources);
        if (false === $action) {
            throw new Omeka_Controller_Exception_404('Invalid action');
        }
        
        // Set the route variables. Namespace the API parameters to prevent 
        // collisions with GET.
        $routeVars = array(
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
     * Return all available API resources and their routing information.
     * 
     * @return array
     */
    public static function getApiResources()
    {
        return apply_filters('api_resources', self::$_apiResources);
    }
    
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
}
