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
     * Handle GET request without ID.
     */
    public function indexAction()
    {
        throw new Exception('Not implemented.');
    }
    
    /**
     * Handle GET request with ID.
     */
    public function getAction()
    {
        $params = $this->getRequest()->getParams();
        $record = $this->_getRecord($params['api_record_type'], $params['api_params'][0]);
        $this->_helper->json($record->getRepresentation());
    }
    
    /**
     * Return the specified record.
     * 
     * @param string $recordType
     * @param int $id
     * @return Omeka_Record_AbstractRecord
     */
    protected function _getRecord($recordType, $id)
    {
        if (!class_exists($recordType)) {
            throw new Exception('Invalid record. Record type not found.');
        }
        $record = $this->_helper->db->getTable($recordType)->find($id);
        if (!$record) {
            throw new Exception('Invalid record. Record not found.');
        }
        if (!($record instanceof Omeka_Api_RecordInterface)) {
            throw new Exception("Invalid record. Record \"$recordName\" must implement Omeka_Api_RecordInterface");
        }
        return $record;
    }
}
