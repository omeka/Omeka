<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class CollectionsController extends Omeka_Controller_AbstractActionController
{
    protected $_autoCsrfProtection = true;

    public $contexts = array('show' => array('omeka-xml'));

    protected $_browseRecordsPerPage = self::RECORDS_PER_PAGE_SETTING;

    public function init()
    {
        $this->_helper->db->setDefaultModelName('Collection');
    }

    /**
     * The show collection action
     */
    public function showAction()
    {
        parent::showAction();
        $currentPage = $this->getParam('page', 1);
        $recordsPerPage = $this->_getBrowseRecordsPerPage('items');
        $itemTable = $this->_helper->db->getTable('Item');
        $params = array('collection' => $this->view->collection->id);
        $items = $itemTable->findBy($params, $recordsPerPage, $currentPage);
        $totalRecords = $itemTable->count($params);

        if ($recordsPerPage) {
            Zend_Registry::set('pagination', array(
                'page' => $currentPage,
                'per_page' => $recordsPerPage,
                'total_results' => $totalRecords,
            ));
        }

        $this->view->items = $items;
    }

    /**
     * The add collection action
     */
    public function addAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getCollectionElementSets();
        parent::addAction();
    }

    /**
     * The edit collection action
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
        if ($collectionTitle != '') {
            return __('The collection "%s" was successfully added!', $collectionTitle);
        } else {
            return __('The collection #%s was successfully added!', strval($collection->id));
        }
    }

    protected function _getEditSuccessMessage($collection)
    {
        $collectionTitle = $this->_getElementMetadata($collection, 'Dublin Core', 'Title');
        if ($collectionTitle != '') {
            return __('The collection "%s" was successfully changed!', $collectionTitle);
        } else {
            return __('The collection #%s was successfully changed!', strval($collection->id));
        }
    }

    protected function _getDeleteSuccessMessage($collection)
    {
        $collectionTitle = $this->_getElementMetadata($collection, 'Dublin Core', 'Title');
        if ($collectionTitle != '') {
            return __('The collection "%s" was successfully deleted!', $collectionTitle);
        } else {
            return __('The collection #%s was successfully deleted!', strval($collection->id));
        }
    }

    protected function _getDeleteConfirmMessage($collection)
    {
        $collectionTitle = $this->_getElementMetadata($collection, 'Dublin Core', 'Title');
        if ($collectionTitle != '') {
            return __('This will delete the collection "%s" and its associated metadata. '
                 . 'This will not delete any items in this collection, but will '
                 . 'delete the reference to this collection in each item.', $collectionTitle);
        } else {
            return __('This will delete the collection #%s and its associated metadata. '
                 . 'This will not delete any items in this collection, but will '
                 . 'delete the reference to this collection in each item.', strval($collection->id));
        }
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
    
    protected function _getBrowseDefaultSort()
    {
        return array('added', 'd');
    }
}
