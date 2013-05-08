<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The default controller for API resources.
 * 
 * @package Omeka\Controller
 */
class ApiController extends Omeka_Controller_AbstractActionController
{
    /**
     * Initialize this controller.
     */
    public function init()
    {
        // Actions should use the jsonApi action helper to render JSON data.
        $this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * Handle GET request without ID.
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $recordType = $request->getParam('api_record_type');
        $resource = $request->getParam('api_resource');
        $page = $request->getQuery('page', 1);
        
        $this->_validateRecordType($recordType);
        
        // Determine the results per page.
        $perPageMax = (int) get_option('api_per_page');
        $perPageUser = (int) $request->getQuery('per_page');
        $perPage = ($perPageUser < $perPageMax && $perPageUser > 0) ? $perPageUser : $perPageMax;
        
        // Get the records and the total record count.
        $recordsTable = $this->_helper->db->getTable($recordType);
        $totalCount = $recordsTable->count($_GET);
        $records = $recordsTable->findBy($_GET, $perPage, $page);
        
        // Set the Link header for pagination.
        $this->_setLinkHeader($perPage, $page, $totalCount, $resource);
        
        // Build the data array.
        $data = array();
        foreach ($records as $record) {
            $data[] = $this->_getRepresentation($record, $resource);
        }
        
        $this->_helper->jsonApi($data);
    }
    
    /**
     * Handle GET request with ID.
     */
    public function getAction()
    {
        $request = $this->getRequest();
        $recordType = $request->getParam('api_record_type');
        $resource = $request->getParam('api_resource');
        $apiParams = $request->getParam('api_params');
        
        $this->_validateRecordType($recordType);
        
        $record = $this->_helper->db->getTable($recordType)->find($apiParams[0]);
        if (!$record) {
            throw new Omeka_Controller_Exception_Api('Invalid record. Record not found.', 404);
        }
        
        $data = $this->_getRepresentation($record, $resource);
        $this->_helper->jsonApi($data);
    }
    
    /**
     * Handle POST requests.
     */
    public function postAction()
    {
        $request = $this->getRequest();
        $recordType = $request->getParam('api_record_type');
        $resource = $request->getParam('api_resource');
        
        $this->_validateRecordType($recordType);
        
        $record = new $recordType;
        $this->_validateUser($record, 'add');
        
        // Set the POST data using the record adapter.
        $recordAdapter = $this->_getRecordAdapter($recordType);
        $recordAdapter->setData($record, $request->getPost());
        
        $record->save();
        $this->_helper->jsonApi($record->id);
    }
    
    /**
     * Validate a record type.
     * 
     * @param string $recordType
     */
    protected function _validateRecordType($recordType)
    {
        if (!class_exists($recordType)) {
            throw new Omeka_Controller_Exception_Api("Invalid record. Record type \"$recordType\" not found.", 404);
        }
        
        // Records must have corresponding record adapters.
        $recordAdapterClass = "Api_$recordType";
        if (!class_exists($recordAdapterClass)) {
           throw new Omeka_Controller_Exception_Api("Invalid record adapter. Record adapter \"$recordAdapterClass\" not found", 404);
        }
        if (!in_array('Omeka_Record_Api_AbstractRecordAdapter', class_parents($recordAdapterClass))) {
           throw new Omeka_Controller_Exception_Api("Invalid record adapter. Record adapter \"$recordAdapterClass\" must extend Omeka_Record_Api_AbstractRecordAdapter", 404);
        }
        
        // POST, PUT, and DELETE methods using the defualt API controller 
        // require records to implement Zend_Acl_Resource_Interface to check 
        // permissions.
        if (in_array($this->getRequest()->getMethod(), array('POST', 'PUT', 'DELETE')) 
            && !in_array('Zend_Acl_Resource_Interface', class_implements($recordType))
        ) {
            throw new Omeka_Controller_Exception_Api("Invalid record. Record \"$recordType\" must implement Zend_Acl_Resource_Interface", 404);
        }
    }
    
    /**
     * Validate a user against a privilege.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param string $privilege
     */
    protected function _validateUser(Omeka_Record_AbstractRecord $record, $privilege)
    {
        $bootstrap = Zend_Registry::get('bootstrap');
        $currentUser = $bootstrap->getResource('CurrentUser');
        $acl = $bootstrap->getResource('Acl');
        if (!$acl->isAllowed($currentUser, $record, $privilege)) {
            throw new Omeka_Controller_Exception_Api('Permission denied.', 403);
        }
    }
    
    protected function _getRecordAdapter($recordType)
    {
        $recordAdapterClass = "Api_$recordType";
        return new $recordAdapterClass;
    }
    
    /**
     * Set the Link header for pagination.
     * 
     * @param int $perPage
     * @param int $page
     * @param int $totalCount
     * @param string $resource
     */
    protected function _setLinkHeader($perPage, $page, $totalCount, $resource)
    {
        // Remove authentication key from query.
        $linkGet = $_GET;
        if (isset($linkGet['key'])) {
            unset($linkGet['key']);
        }
        
        // Calculate the first, last, prev, and next page numbers.
        $linkPages = array(
            'first' => 1, 
            'last' => ceil($totalCount / $perPage), 
        );
        if (1 < $page) {
            $linkPages['prev'] = $page - 1;
        }
        if ($page < $linkPages['last']) {
            $linkPages['next'] = $page + 1;
        }
        
        // Build the Link value.
        $linkValues = array();
        foreach ($linkPages as $rel => $page) {
            $linkQuery = array_merge($linkGet, array('page' => $page, 'per_page' => $perPage));
            $linkValues[] = "<" . absolute_url("api/$resource", $linkQuery) . ">; rel=\"$rel\"";
        }
        
        $this->getResponse()->setHeader('Link', implode(', ', $linkValues));
    }
    
    /**
     * Get the representation of a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param string $resource
     */
    protected function _getRepresentation(Omeka_Record_AbstractRecord $record, $resource)
    {
        $extend = array();
        $extendTemp = apply_filters("api_extend_$resource", array(), array('record' => $record));
        $apiResources = $this->getFrontController()->getParam('api_resources');
        
        // Validate each extended resource. Each must be registered as an API 
        // resource and the content must contain "id" and "url" for one resource 
        // or "count" and "url" for multiple resources.
        foreach ($extendTemp as $extendResource => $extendContent) {
            if (is_array($extendContent) 
                && array_key_exists($extendResource, $apiResources) 
                && (array_key_exists('count', $extendContent) || array_key_exists('id', $extendContent))
                && array_key_exists('url', $extendContent)
            ) {
                $extend[$extendResource] = array('url' => $extendContent['url']);
                if (array_key_exists('id', $extendContent)) {
                    $extend[$extendResource]['id'] = $extendContent['id'];
                } else {
                    $extend[$extendResource]['count'] = $extendContent['count'];
                }
            }
        }
        
        // Get the representation from the record adapter.
        $recordAdapter = $this->_getRecordAdapter(get_class($record));
        $representation = $recordAdapter->getRepresentation($record);
        $representation['extended_resources'] = $extend;
        return $representation;
    }
}
