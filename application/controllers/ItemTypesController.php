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
class ItemTypesController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ItemType');     
    }

    public function addAction()
    {
        $itemType = new ItemType;
        $form = $this->_getForm($itemType);
        
        if (isset($_POST[Omeka_Form_ItemTypes::SUBMIT_ADD_ELEMENT_ID])) {
            if ($form->isValid($_POST)) {
                try{
                    $itemType = $form->saveFromPost();                    
                    $this->_helper->flashMessenger(__('The item type "%s" was successfully added.', $itemType->name), 'success');
                    $this->_helper->redirector('show', null, null, array('id'=>$itemType->id));
                } catch (Omeka_Validate_Exception $e) {
                    $this->_helper->flashMessenger($e);
                }                
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        
        // specify view variables
        $this->view->form = $form;
        $this->view->item_type = $itemType;
    }
    
    public function editAction()
    {        
        // get the item type
        $itemType = $this->_helper->db->findById();
        
        // edit the item type
        $form = $this->_getForm($itemType);
        if (isset($_POST[Omeka_Form_ItemTypes::SUBMIT_EDIT_ELEMENT_ID])) {
            if ($form->isValid($_POST)) {
                try{                    
                    $form->saveFromPost();                    
                    $this->_helper->flashMessenger(__('The item type "%s" was successfully updated.', $itemType->name), 'success');
                    $this->_helper->redirector('show', null, null, array('id'=>$itemType->id));
                } catch (Omeka_Validate_Exception $e) {
                    $this->_helper->flashMessenger($e);
                }                
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        
        // specify view variables
        $this->view->form = $form;
        $this->view->item_type = $itemType;
    }

    public function addNewElementAction()
    {
        if ($this->_getParam('from_post') == 'true') {
            $elementTempId = $this->_getParam('elementTempId');
            $elementName = $this->_getParam('elementName');
            $elementDescription = $this->_getParam('elementDescription');
            $elementOrder = $this->_getParam('elementOrder');
        } else {
            $elementTempId = '' . time();
            $elementName = '';
            $elementDescription = '';
            $elementOrder = intval($this->_getParam('elementCount')) + 1;
        }

        $elementNameId =  Omeka_Form_ItemTypes::ADD_NEW_ELEMENT_NAME_PREFIX . $elementTempId;
        $elementNameValue = $elementName;

        $elementDescriptionId = Omeka_Form_ItemTypes::ADD_NEW_ELEMENT_DESCRIPTION_PREFIX . $elementTempId;
        $elementDescriptionValue = $elementDescription;

        $elementOrderId = Omeka_Form_ItemTypes::ADD_NEW_ELEMENT_ORDER_PREFIX . $elementTempId;
        $elementOrderValue = $elementOrder;

        $this->view->assign(array('elementNameId' => $elementNameId,
                                  'elementNameValue' => $elementNameValue,
                                  'elementDescriptionId' => $elementDescriptionId,
                                  'elementDescriptionValue' => $elementDescriptionValue,
                                  'elementOrderId' => $elementOrderId,
                                  'elementOrderValue' => $elementOrderValue,
                                   ));
    }

    public function addExistingElementAction()
    {
        if ($this->_getParam('from_post') == 'true') {
            $elementTempId = $this->_getParam('elementTempId');
            $elementId = $this->_getParam('elementId');            
            $element = $this->_helper->db->getTable('Element')->find($elementId);
            if ($element) {
                $elementDescription = $element->description;
            }
            $elementOrder = $this->_getParam('elementOrder');
        } else {
            $elementTempId = '' . time();
            $elementId = '';
            $elementDescription = '';
            $elementOrder = intval($this->_getParam('elementCount')) + 1;
        }

        $elementNameId = Omeka_Form_ItemTypes::ADD_EXISTING_ELEMENT_ID_PREFIX . $elementTempId;
        $elementNameValue = $elementId;

        $elementOrderId = Omeka_Form_ItemTypes::ADD_EXISTING_ELEMENT_ORDER_PREFIX . $elementTempId;
        $elementOrderValue = $elementOrder;

        $this->view->assign(array('elementNameId' => $elementNameId,
                                  'elementNameValue' => $elementNameValue,
                                  'elementDescription' => $elementDescription,
                                  'elementOrderId' => $elementOrderId,
                                  'elementOrderValue' => $elementOrderValue,
                                  ));
    }

    public function changeExistingElementAction()
    {
        $elementId = $this->_getParam('elementId');
        $element = $this->_helper->db->getTable('Element')->find($elementId);

        $elementDescription = '';
        if ($element) {
            $elementDescription = $element->description;
        }

        $data = array();
        $data['elementDescription'] = $elementDescription;

        $this->_helper->json($data);
    }

    public function elementListAction()
    {
        $itemTypeId = $this->_getParam('item-type-id');
        if ($itemTypeId) {
            $itemType = $this->_helper->db->findById($itemTypeId);
        } else {
            $itemType = null;
        }
        $this->view->item_type = $itemType;
        if ($itemType) {
            $this->view->elements = $itemType->Elements;
        } else {
            $this->view->elements = array();
        }
    }
    
    protected function _redirectAfterAdd($itemType)
    {
        $this->_redirect("item-types/edit/{$itemType->id}");
    }

    protected function _getDeleteConfirmMessage($itemType)
    {
        return __('This will delete the "%s" item type but will not delete the '
             . 'elements assigned to the item type. Items that are assigned to '
             . 'this item type will lose all metadata that is specific to the '
             . 'item type.', $itemType->name);
    }

    protected function _getAddSuccessMessage($itemType)
    {
        return __('The item type "%s" was successfully added!  You may now add elements to your new item type.', $itemType->name);
    }
    
    private function _getForm($itemType)
    {        
        require_once APP_DIR . '/forms/ItemTypes.php';
        $form = new Omeka_Form_ItemTypes;
        $form->setItemType($itemType);
        fire_plugin_hook('item_types_form', array('form' => $form));
        return $form;
    }
}
