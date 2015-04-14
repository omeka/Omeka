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
     * @var The default API resources and their routing information.
     * 
     * Use the "api_resources" filter to add resources, following this format:
     * 
     * <code>
     * // For the path: /api/your_resources/:id
     * 'your_resources' => array(
     *     // Module associated with your resource.
     *     'module' => 'your-plugin-name', 
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
     *     // List of GET parameters available for your index action.
     *     'index_params' => array('foo', 'bar'), 
     * )
     * </code>
     * 
     * If not given, "module" and "controller" fall back to their defaults, 
     * "default" and "api". Resources using the default controller MUST include 
     * a "record_type". Remove "actions" that are not wanted or not implemented.
     */
    protected static $_apiResources = array(
        'site' => array(
            'controller' => 'site', 
            'actions' => array('index'), 
        ), 
        'resources' => array(
            'controller' => 'resources', 
            'actions' => array('index')
        ), 
        'collections' => array(
            'record_type' => 'Collection', 
            'actions' => array('index', 'get', 'post', 'put', 'delete'), 
            'index_params' => array(
                'public','featured','added_since','modified_since','owner',
            ),
        ), 
        'items' => array(
            'record_type' => 'Item', 
            'actions' => array('index', 'get', 'post', 'put', 'delete'), 
            'index_params' => array(
                'collection', 'item_type', 'featured', 'public', 'added_since',
                'modified_since', 'owner', 'tags', 'excludeTags', 'hasImage',
                'range'
            ), 
        ), 
        'files' => array(
            'controller' => 'files', 
            'record_type' => 'File', 
            'actions' => array('index', 'get', 'post', 'put', 'delete'),
            'index_params' => array(
                'item', 'order', 'size_greater_than', 'has_derivative_image',
                'mime_type', 'modified_since', 'added_since', 
            ), 
        ), 
        'item_types' => array(
            'record_type' => 'ItemType',
            'actions' => array('index', 'get', 'post', 'put', 'delete'),
            'index_params' => array('name'), 
        ),
        'elements' => array(
            'record_type' => 'Element',
            'actions' => array('index', 'get', 'post', 'put', 'delete'),
            'index_params' => array('element_set', 'name', 'item_type'), 
        ),
        'element_sets' => array(
            'record_type' => 'ElementSet',
            'actions' => array('index', 'get', 'post', 'delete'),
            'index_params' => array('name', 'record_type'), 
        ),
        'users' => array(
            'record_type' => 'User',
            'actions' => array('get'),
        ), 
        'tags' => array(
            'record_type' => 'Tag', 
            'actions' => array('index', 'get', 'delete'), 
        ), 
    );
    
    /**
     * Handle API-specific controller logic.
     * 
     * Via Omeka_Application_Resource_Frontcontroller, this plugin is only 
     * registered during an API request.
     * 
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        
        // Set the available API resources as a front controller param so they 
        // are applied only once and globally accessible.
        $front->setParam('api_resources', self::getApiResources());
        
        // Set the API controller directories.
        $apiControllerDirectories = array();
        $controllerDirectories = $front->getControllerDirectory();
        foreach ($controllerDirectories as $module => $controllerDirectory) {
            $apiControllerDirectories[$module] = "$controllerDirectory/api";
        }
        $front->setControllerDirectory($apiControllerDirectories);
    }
    
    /**
     * Get the filtered API resources.
     * 
     * @return array
     */
    public static function getApiResources()
    {
        return apply_filters('api_resources', self::$_apiResources);
    }
}

