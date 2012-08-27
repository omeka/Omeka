<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ItemTypesController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ItemType');     
    }

    public function addAction()
    {
        $itemType = null;
        $form = $this->_getForm($itemType);
        
        if (isset($_POST[Omeka_Form_ItemTypes::SUBMIT_ADD_ELEMENT_ID])) {
            if ($form->isValid($_POST)) {
                try{
                    $itemType = $form->saveFromPost();                    
                    $this->_helper->flashMessenger(__('The item type "%s" was successfully added.', $itemType->name), 'success');
                    $this->_helper->redirector('show', null, null, array('id'=>$itemType->id));
                } catch (Omeka_Validator_Exception $e) {
                    $this->_helper->flashMessenger($e);
                }                
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        
        // specify view variables
        $this->view->form = $form;
        $this->view->itemtype = $itemType;
    }
    
    public function editAction()
    {        
        // get the item type
        $itemType = $this->_helper->db->findById();
        
        // check to see if the item type should be deleted
        if (isset($_POST[Omeka_Form_ItemTypes::DELETE_ELEMENT_ID])) {
            $this->_redirect("item-types/delete-confirm/{$itemType->id}");
        }
        
        // edit the item type        
        $form = $this->_getForm($itemType);
        if (isset($_POST[Omeka_Form_ItemTypes::SUBMIT_EDIT_ELEMENT_ID])) {
            if ($form->isValid($_POST)) {
                
                try{                    
                    $form->saveFromPost();                    
                    $this->_helper->flashMessenger(__('The item type "%s" was successfully updated.', $itemType->name), 'success');
                    $this->_helper->redirector('show', null, null, array('id'=>$itemType->id));
                } catch (Omeka_Validator_Exception $e) {
                    $this->_helper->flashMessenger($e);
                }                
            } else {
                $this->_helper->flashMessenger(__('There were errors found in your form. Please edit and resubmit.'), 'error');
            }
        }
        
        // specify view variables
        $this->view->form = $form;
        $this->view->itemtype = $itemType;
    }

    public function addNewElementAction()
    {
        $elementCount = intval($this->_getParam('elementCount'));

        if ($this->_getParam('from_post') == 'true') {
            $elementTempId = $this->_getParam('elementTempId');
            $elementName = $this->_getParam('elementName');
            $elementDescription = $this->_getParam('elementDescription');
            $elementOrder = $this->_getParam('elementOrder');
        } else {
            $elementTempId = '' . time();
            $elementName = '';
            $elementDescription = '';
            $elementOrder = $elementCount + 1;
        }

        $this->view->assign(array('elementTempId' => $elementTempId,
                                  'elementName' => $elementName,
                                  'elementDescription' => $elementDescription,
                                  'elementOrder' => $elementOrder,
                                  'addNewElementNamePrefix' => Omeka_Form_ItemTypes::ADD_NEW_ELEMENT_NAME_PREFIX,
                                  'addNewElementDescriptionPrefix' => Omeka_Form_ItemTypes::ADD_NEW_ELEMENT_DESCRIPTION_PREFIX,
                                  'addNewElementOrderPrefix' => Omeka_Form_ItemTypes::ADD_NEW_ELEMENT_ORDER_PREFIX
                                   ));
    }

    public function addExistingElementAction()
    {
        $elementCount = intval($this->_getParam('elementCount'));

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
            $elementOrder = $elementCount + 1;
        }

        $this->view->assign(array('elementTempId' => $elementTempId,
                                  'elementId' => $elementId,
                                  'elementDescription' => $elementDescription,
                                  'elementOrder' => $elementOrder,
                                  'addExistingElementIdPrefix' => Omeka_Form_ItemTypes::ADD_EXISTING_ELEMENT_ID_PREFIX,
                                  'addExistingElementOrderPrefix' => Omeka_Form_ItemTypes::ADD_EXISTING_ELEMENT_ORDER_PREFIX
                                  ));
    }

    public function changeExistingElementAction()
    {
        $elementId = $this->_getParam('elementId');
        // $elementTempId = $this->_getParam('elementTempId');

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
        $this->view->itemtype = $itemType;
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
        fire_plugin_hook('itemtypes_form', array('form' => $form));
        return $form;
    }
}
