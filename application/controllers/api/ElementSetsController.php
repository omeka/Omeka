<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

require_once 'ApiController.php';

/**
 * The controller for API /element_sets.
 * 
 * @package Omeka\Controller
 */
class ElementSetsController extends ApiController
{
    /**
     * Handle DELETE requests.
     */
    public function deleteAction()
    {
        $apiParams = $this->getRequest()->getParam('api_params');
        
        $elementSet = $this->_helper->db->getTable('ElementSet')->find($apiParams[0]);
        if (!$elementSet) {
            throw new Omeka_Controller_Exception_Api('Invalid record. Record not found.', 404);
        }
        
        if (in_array($elementSet->name, array('Dublin Core', ElementSet::ITEM_TYPE_NAME))) {
            throw new Omeka_Controller_Exception_Api('Invalid record. "Dublin Core" and "Item Type Metadata" element sets may not be deleted.', 404);
        }
        
        // The user must have permission to delete this record.
        $this->_validateUser($elementSet, 'delete');
        
        $elementSet->delete();
        
        // 204 No Content.
        $this->getResponse()->setHttpResponseCode(204);
    }
}
