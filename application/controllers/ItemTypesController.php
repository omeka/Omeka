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
    }
    
    /**
     * Add the item type and redirect to the item type edit page so the user can 
     * assign new and existing elements.
     * 
     * Optimal behavior is for it to be possible to add existing elements and 
     * create new elements within the item type add form. This is a temporary 
     * hack and should be removed once the item type forms are in full working 
     * order. 
     */
    public function addAction()
    {
        $itemType = new ItemType();
        try {
            if ($itemType->saveForm($_POST)) {
                $this->flash('You may now add elements to your new item type.');
                $this->_redirect("item-types/edit/{$itemType->id}");
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        } catch (Exception $e) {
            $this->flash($e->getMessage());
        }
        $this->view->assign(array('itemtype' => $itemType));            
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
                
                $itemType->addElementById($element->id);
                
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
            $element->setElementSet(ELEMENT_SET_ITEM_TYPE);
            $element->setRecordType('Item');
            $element->setName((string)$this->_getParam('element-name'));
            $element->setDescription((string)$this->_getParam('element-description'));
            $element->data_type_id = (int)$this->_getParam('element-data-type-id');            
            $element->forceSave();
        }   
        
        return $element;     
    }
}