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
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                try{
                    $itemType = $form->saveFromPost();                    
                    $this->_helper->flashMessenger(__('The item type "%s" was successfully added.', $itemType->name), 'success');
                    $this->_helper->redirector('show', null, null, array('id'=>$itemType->id));
                } catch (Omeka_Validate_Exception $e) {
                    $itemType->delete();
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
        if ($this->getRequest()->isPost()) {
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

        $stem = Omeka_Form_ItemTypes::NEW_ELEMENTS_INPUT_NAME . "[$elementTempId]";
        $elementNameName = $stem . '[name]';
        $elementDescriptionName = $stem . '[description]';
        $elementOrderName = $stem . '[order]';

        $this->view->assign(array('element_name_name' => $elementNameName,
                                  'element_name_value' => $elementName,
                                  'element_description_name' => $elementDescriptionName,
                                  'element_description_value' => $elementDescription,
                                  'element_order_name' => $elementOrderName,
                                  'element_order_value' => $elementOrder,
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

        $stem = Omeka_Form_ItemTypes::ELEMENTS_TO_ADD_INPUT_NAME . "[$elementTempId]";
        $elementIdName = $stem .'[id]';
        $elementOrderName = $stem .'[order]';

        $this->view->assign(array('element_id_name' => $elementIdName,
                                  'element_id_value' => $elementId,
                                  'element_description' => $elementDescription,
                                  'element_order_name' => $elementOrderName,
                                  'element_order_value' => $elementOrder,
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
