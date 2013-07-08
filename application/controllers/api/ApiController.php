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
        
        // Get the records and the result count.
        $recordsTable = $this->_helper->db->getTable($recordType);
        $totalResults = $recordsTable->count($_GET);
        $records = $recordsTable->findBy($_GET, $perPage, $page);
        
        // Set the non-standard Omeka-Total-Results header.
        $this->getResponse()->setHeader('Omeka-Total-Results', $totalResults);
        
        // Set the Link header for pagination.
        $this->_setLinkHeader($perPage, $page, $totalResults, $resource);
        
        // Build the data array.
        $data = array();
        $recordAdapter = $this->_getRecordAdapter($recordType);
        foreach ($records as $record) {
            $data[] = $this->_getRepresentation($recordAdapter, $record, $resource);
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
        
        // The user must have permission to show this record.
        $this->_validateUser($record, 'show');
        
        $data = $this->_getRepresentation($this->_getRecordAdapter($recordType), $record, $resource);
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
        
        // The user must have permission to add this record.
        $this->_validateUser($record, 'add');
        
        // The request body must be a JSON object.
        $data = json_decode($request->getRawBody());
        if (!($data instanceof stdClass)) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Request body must be a JSON object.', 400);
        }
        
        // Set the POST data to the record using the record adapter.
        $this->_getRecordAdapter($recordType)->setPostData($record, $data);
        if (!$record->save(false)) {
            throw new Omeka_Controller_Exception_Api('Error when saving record.', 
                400, $record->getErrors()->get());
        }
        
        // The client may have set invalid data to the record. This does not 
        // always throw an error. Get the current record state directly from the 
        // database.
        $data = $this->_getRepresentation(
            $this->_getRecordAdapter($recordType), 
            $this->_helper->db->getTable($recordType)->find($record->id), 
            $resource
        );
        $this->getResponse()->setHttpResponseCode(201);
        $this->getResponse()->setHeader('Location', $data['url']);
        $this->_helper->jsonApi($data);
    }
    
    /**
     * Handle PUT requests.
     */
    public function putAction()
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
        
        // The user must have permission to edit this record.
        $this->_validateUser($record, 'edit');
        
        // The request body must be a JSON object.
        $data = json_decode($request->getRawBody());
        if (!($data instanceof stdClass)) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Request body must be a JSON object.', 400);
        }
        
        // Set the PUT data to the record using the record adapter.
        $this->_getRecordAdapter($recordType)->setPutData($record, $data);
        if (!$record->save(false)) {
            throw new Omeka_Controller_Exception_Api('Error when saving record.', 
                400, $record->getErrors()->get());
        }
        
        // The client may have set invalid data to the record. This does not 
        // always throw an error. Get the current record state directly from the 
        // database.
        $data = $this->_getRepresentation(
            $this->_getRecordAdapter($recordType), 
            $this->_helper->db->getTable($recordType)->find($record->id), 
            $resource
        );
        $this->_helper->jsonApi($data);
    }
    
    /**
     * Handle DELETE requests.
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $recordType = $request->getParam('api_record_type');
        $apiParams = $request->getParam('api_params');
        
        $this->_validateRecordType($recordType);
        
        $record = $this->_helper->db->getTable($recordType)->find($apiParams[0]);
        if (!$record) {
            throw new Omeka_Controller_Exception_Api('Invalid record. Record not found.', 404);
        }
        
        // The user must have permission to delete this record.
        $this->_validateUser($record, 'delete');
        
        $record->delete();
        
        // 204 No Content.
        $this->getResponse()->setHttpResponseCode(204);
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
           throw new Omeka_Controller_Exception_Api("Invalid record adapter. Record adapter \"$recordAdapterClass\" not found.", 404);
        }
        if (!in_array('Omeka_Record_Api_RecordAdapterInterface', class_implements($recordAdapterClass))) {
           throw new Omeka_Controller_Exception_Api("Invalid record adapter. Record adapter \"$recordAdapterClass\" is invalid", 500);
        }
    }
    
    /**
     * Validate a user against a privilege.
     * 
     * For GET requests, assume that records without an ACL resource do not 
     * require a permission check. Note that for POST, PUT, and DELETE, all 
     * records must define an ACL resource.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param string $privilege
     */
    protected function _validateUser(Omeka_Record_AbstractRecord $record, $privilege)
    {
        $bootstrap = Zend_Registry::get('bootstrap');
        $currentUser = $bootstrap->getResource('CurrentUser');
        $acl = $bootstrap->getResource('Acl');
        
        if ($record instanceof Zend_Acl_Resource_Interface) {
            if (!$acl->isAllowed($currentUser, $record, $privilege)) {
                throw new Omeka_Controller_Exception_Api('Permission denied.', 403);
            }
        } elseif (in_array($this->getRequest()->getMethod(), array('POST', 'PUT', 'DELETE'))) {
            $recordType = get_class($record);
            throw new Omeka_Controller_Exception_Api("Invalid record. Record \"$recordType\" must define an ACL resource.", 500);
        }
    }
    
    /**
     * Get the adapter for a record type.
     * 
     * @param string $recordType
     * @return Omeka_Record_Api_AbstractRecordAdapter
     */
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
     * @param int $totalResults
     * @param string $resource
     */
    protected function _setLinkHeader($perPage, $page, $totalResults, $resource)
    {
        // Remove authentication key from query.
        $linkGet = $_GET;
        if (isset($linkGet['key'])) {
            unset($linkGet['key']);
        }
        
        // Calculate the first, last, prev, and next page numbers.
        $linkPages = array(
            'first' => 1, 
            'last' => ceil($totalResults / $perPage), 
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
    protected function _getRepresentation(
        Omeka_Record_Api_AbstractRecordAdapter $recordAdapter, 
        Omeka_Record_AbstractRecord $record, 
        $resource
    ) {
        $extend = array();
        $extendTemp = apply_filters("api_extend_$resource", array(), array('record' => $record));
        $apiResources = $this->getFrontController()->getParam('api_resources');
        
        // Validate each extended resource. Each must be registered as an API 
        // resource and the content must contain "id" and "url" for one resource 
        // or "count" and "url" for multiple resources. A "resource" is 
        // recommended but not mandatory. Everything else passes through as 
        // custom data that may be used for the client's convenience.
        foreach ($extendTemp as $extendResource => $extendContent) {
            if (is_array($extendContent) 
                && array_key_exists($extendResource, $apiResources) 
                && (array_key_exists('count', $extendContent) || array_key_exists('id', $extendContent))
                && array_key_exists('url', $extendContent)
            ) {
                $extend[$extendResource] = $extendContent;
            }
        }
        
        // Get the representation from the record adapter.
        $representation = $recordAdapter->getRepresentation($record);
        $representation['extended_resources'] = $extend;
        return $representation;
    }
}
