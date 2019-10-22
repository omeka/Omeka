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
     * Name for file on a multipart/form-data request.
     */
    const FILE_NAME = 'file';

    /**
     * Name for JSON data on a multipart/form-data request.
     */
    const DATA_NAME = 'data';

    /**
     * Handle POST requests.
     */
    public function postAction()
    {
        $bootstrap = Zend_Registry::get('bootstrap');
        $adapter = $this->_getRecordAdapter('File');

        // Check for valid file.
        if (!isset($_FILES[self::FILE_NAME]['name']) || is_array($_FILES[self::FILE_NAME]['name'])) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Exactly one file must be uploaded per request.', 400);
        }

        // Check for valid JSON data.
        if (!isset($_POST[self::DATA_NAME])) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Missing JSON data.', 400);
        }
        $data = json_decode($_POST[self::DATA_NAME]);
        if (!($data instanceof stdClass)) {
            throw new Omeka_Controller_Exception_Api('Invalid request. Request body must be a JSON object.', 400);
        }

        $db = $bootstrap->getResource('Db');

        // Check for valid item.
        if (!isset($data->item->id) && !is_object($data->item->id)) {
            throw new Omeka_Controller_Exception_Api('Invalid item. File must belong to an existing item.', 400);
        }
        $item = $db->getTable('Item')->find($data->item->id);
        if (!$item) {
            throw new Omeka_Controller_Exception_Api('Invalid item. File must belong to an existing item.', 400);
        }

        // The user must have permission to edit the owner item.
        $this->_validateUser($item, 'edit');

        $builder = new Builder_Item($db);
        $builder->setRecord($item);
        $fileInfo['source'] = self::FILE_NAME;
        $fileInfo['metadata'] = $adapter->extractElementTextData($data);
        if (isset($data->order)) {
            $fileInfo['order'] = $data->order;
        }
        $files = $builder->addFiles('Upload', $fileInfo);
        $record = $this->_helper->db->getTable('File')->find($files[0]->id);

        $data = $this->_getRepresentation($adapter, $record, 'files');
        $this->getResponse()->setHttpResponseCode(201);
        $this->getResponse()->setHeader('Location', $data['url']);
        $this->_helper->jsonApi($data);
    }
}
