<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Omeka_Controller_Action
 **/
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemTypesController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'ItemType';
        
        // $contextSwitcher = $this->_helper->getHelper('contextSwitch');
        // $contextArray = array(
        //             'browse' => array('xml', 'json', 'dc', 'rss2'),
        //             'show'   => array('xml', 'json', 'dc'));
        // $contextSwitcher->addActionContexts($contextArray);
    }
    
    /**
     * Add an element to an item type.  This could either be a new Element or an existing one.
     * 
     * Post can contain:
     *      status = "new" 
     *      name = "New name"
     *      description = "New description"
     *      item_type_id = #
     *      order = #     
     * 
     *      status = "existing"
     *      element_id = #
     *      item_type_id = #
     *      order = #     
     * 
     * @return void
     **/
    public function addElementAction()
    {
        $itemTypeId = (int)$this->_getParam('item-type-id');
        
        // This should throw an exception if the item type is not valid.
        $itemType = $this->findById($itemTypeId);
        $this->view->itemtype = $itemType;
        
        // Retrieve a list of the data types that we can use for creating new fields in Omeka.
        $this->view->datatypes = $this->getDb()->getTable('DataType')->findPairsForSelectForm();
        
        if (!$_POST) {
            $this->render('element-form');
        } else {
            // Submit the post to create a new element and a new join on the item_types_elements table.
            try {
                // Try to get the Element record based on the form submission.
                $element = new Element;
                $element = $this->getElementFromPost($element);
                
                $itemType->addElement($element->id);
                
            } catch (Omeka_Validator_Exception $e) {
                $errors = (string)$element->getErrors();
                $this->flashValidationErrors($e);
            } catch (Exception $e) {
                $errors = $e->getMessage();
                $this->flash($errors, Omeka_Controller_Flash::GENERAL_ERROR);
            }
            
            // For a valid form submission
            if (!isset($errors)) {
                //Check if we're an ajax request
                
                // If this is an AJAX request, re-render the partial that displays
                // the list of elements for this item type.
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return $this->_forward('element-list', null, null, array('item-type-id'=>$itemTypeId));
                } else {
                    $this->redirect->goto('show', null, null, array('id'=>$itemTypeId));
                }
            } else {
                // If we have an invalid form submission.
                $this->getResponse()->setHttpResponseCode(422);
                $this->render('element-form');
            }
        }
    }
    
    public function elementListAction()
    {
        $itemTypeId = $this->_getParam('item-type-id');
        $itemType = $this->findById($itemTypeId);
        $this->view->itemtype = $itemType;
        $this->view->elements = $itemType->Elements;
    }
    
    public function deleteElementAction()
    {
        $itemType = $this->findById($this->_getParam('item-type-id'));
        $elementId = (int)$this->_getParam('element-id');
                
        $itemType->removeElement($elementId);
        
        // If this is an AJAX request, render the element list again.
        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->_forward('element-list', null, null, array('item-type-id'=>$itemType->id));
        } else {
            // If this is a normal HTTP request, redirect to the show page.
            return $this->redirect->goto('show', null, null, array('id'=>$itemType->id));
        }
    }
    
    protected function getElementFromPost($element)
    {
        // If we are adding an existing element to this item type.
        if ($elementId = (int) $this->_getParam('element-id')) {
            $element = $this->findById($elementId, 'Element');
        } else {            
            $elementSet = $this->getItemTypeElementSet();
            
            $element->name = (string)$this->_getParam('element-name');
            $element->description = (string)$this->_getParam('element-description');
            $element->record_type_id = (int)$this->getItemRecordTypeId();
            $element->element_set_id = $elementSet->id;
            $element->data_type_id = (int)$this->_getParam('element-data-type-id');
            // var_dump($element);exit;
            
            $element->forceSave();
        }   
        
        return $element;     
    }
    
    protected function getItemTypeElementSet()
    {
        // Element should belong to the 'Item Type' element set.
        return $this->getDb()->getTable('ElementSet')->findBySql('name = ?', array('Item Type'), true);
    }
    
    protected function getItemRecordTypeId()
    {
        return $this->getDb()->getTable('RecordType')->findIdFromName('Item');
    }
}