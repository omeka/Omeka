<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

require_once 'ApiController.php';

/**
 * The controller for API /files.
 * 
 * @package Omeka\Controller
 */
class FilesController extends ApiController
{
    /**
     * Key for file on a multipart/form-data request.
     */
    const FILE_KEY = 'file';
    
    /**
     * Key for JSON data on a multipart/form-data request.
     */
    const DATA_KEY = 'data';
    
    /**
     * Handle POST requests.
     */
    public function postAction()
    {
        // Check for valid file.
        if (!isset($_FILES[self::FILE_KEY]['name']) || is_array($_FILES[self::FILE_KEY]['name'])) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Exactly one file must be uploaded per request.', 404);
        }
        
        // Check for valid JSON data.
        if (!isset($_POST[self::DATA_KEY])) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Missing JSON data.', 404);
        }
        $data = json_decode($_POST[self::DATA_KEY]);
        if (!($data instanceof stdClass)) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Request body must be a JSON object.', 400);
        }
        
        $db = get_db();
        
        // Check for valid item.
        if (!isset($data->item->id) && !is_object($data->item->id)) {
            throw new Omeka_Controller_Exception_Api('Invalid item. File must belong to an existing item.', 404);
        }
        $item = $db->getTable('Item')->find($data->item->id);
        if (!$item) {
            throw new Omeka_Controller_Exception_Api('Invalid item. File must belong to an existing item.', 404);
        }
        
        // The user must have permission to edit the owner item.
        $this->_validateUser($item, 'edit');
        
        $builder = new Builder_Item($db);
        $builder->setRecord($item);
        $files = $builder->addFiles('Upload', self::FILE_KEY);
        $record = $files[0];
        
        // Set the POST data to the record using the record adapter.
        $this->_getRecordAdapter('File')->setData($record, $data);
        if (!$record->save(false)) {
            throw new Omeka_Controller_Exception_Api('Error when saving record.', 
                400, $record->getErrors()->get());
        }
        
        // The client may have set invalid data to the record. This does not 
        // always throw an error. Get the current record state directly from the 
        // database.
        $data = $this->_getRepresentation(
            $this->_getRecordAdapter('File'), 
            $this->_helper->db->getTable('File')->find($record->id), 
            'files'
        );
        $this->getResponse()->setHttpResponseCode(201);
        $this->getResponse()->setHeader('Location', $data['url']);
        $this->_helper->jsonApi($data);
    }
}
