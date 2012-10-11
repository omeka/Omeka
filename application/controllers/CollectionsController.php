<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * CRUD controller for Collections
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class CollectionsController extends Omeka_Controller_AbstractActionController
{
    public $contexts = array('show' => array('omeka-xml', 'omeka-json'));
    
    protected $_browseRecordsPerPage = 10;
        
    public function init()
    {
        $this->_helper->db->setDefaultModelName('Collection');     
    }
    
    /**
     * The browse collections action.
     * 
     */
    public function browseAction()
    {
        if (!$this->_getParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
            $this->_setParam('sort_dir', 'd');
        }
        
        parent::browseAction();
    }
    
    /**
     * The show collection action
     * 
     */
    public function showAction()
    {
        parent::showAction();
        $this->view->items = $this->_helper->db->getTable('Item')->findBy(
            array('collection' => $this->view->collection->id), is_admin_theme() ? 10 : 5);
    }
    
    /**
     * The add collection action
     * 
     */
    public function addAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getCollectionElementSets();
        parent::addAction();
    }
    
    /**
     * The edit collection action
     * 
     */
    public function editAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getCollectionElementSets();
        parent::editAction();
    }
    
    protected function _getAddSuccessMessage($collection)
    {
        $collectionTitle = $this->_getElementMetadata($collection, 'Dublin Core', 'Title');
        return __('The collection "%s" was successfully added!', $collectionTitle);
    }
    
    protected function _getEditSuccessMessage($collection)
    {
        $collectionTitle = $this->_getElementMetadata($collection, 'Dublin Core', 'Title');
        return __('The collection "%s" was successfully changed!', $collectionTitle);        
    }
    
    protected function _getDeleteSuccessMessage($collection)
    {
        $collectionTitle = $this->_getElementMetadata($collection, 'Dublin Core', 'Title');
        return __('The collection "%s" was successfully deleted!', $collectionTitle);        
    }

    protected function _getDeleteConfirmMessage($collection)
    {
        return __('This will delete the collection and its associated metadata. '
             . 'This will not delete any items in this collection, but will '
             . 'delete the reference to this collection in each item.');
    }
    
    protected function _getElementMetadata($collection, $elementSetName, $elementName) 
    {
        $m = new Omeka_View_Helper_Metadata;
        return strip_formatting($m->metadata($collection, array($elementSetName, $elementName)));
    }
        
    /**
     * Gets the element sets for the 'Collection' record type.
     * 
     * @return array The element sets for the 'Collection' record type
     */
    protected function _getCollectionElementSets()
    {
        return $this->_helper->db->getTable('ElementSet')->findByRecordType('Collection');
    }
}
