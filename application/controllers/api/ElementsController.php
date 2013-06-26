<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

require_once 'ApiController.php';

/**
 * The controller for API /elements.
 * 
 * @package Omeka\Controller
 */
class ElementsController extends ApiController
{
    /**
     * Handle DELETE requests.
     */
    public function deleteAction()
    {
        $apiParams = $this->getRequest()->getParam('api_params');
        
        $element = $this->_helper->db->getTable('Element')->find($apiParams[0]);
        if (!$element) {
            throw new Omeka_Controller_Exception_Api('Invalid record. Record not found.', 404);
        }
        
        $elementSet = $file->getTable('ElementSet')->findBy(array('name' => ElementSet::ITEM_TYPE_NAME));
        if ($element->element_set_id != $elementSet->id) {
            throw new Omeka_Controller_Exception_Api('Invalid record. Only elements belonging to the "Item Type Metadata" element set may be deleted.', 400);
        }
        
        // The user must have permission to delete this record.
        $this->_validateUser($file, 'delete');
        
        $file->delete();
        
        // 204 No Content.
        $this->getResponse()->setHttpResponseCode(204);
    }
}
