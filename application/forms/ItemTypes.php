<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Item types form.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_ItemTypes extends Omeka_Form
{
    // form id
    const FORM_ID = 'item-type-form';

    // form element ids
    const NAME_ELEMENT_ID = 'itemtypes_name';
    const DESCRIPTION_ELEMENT_ID = 'itemtypes_description';
    const REMOVE_HIDDEN_ELEMENT_ID = 'itemtypes_remove';
    const SUBMIT_EDIT_ELEMENT_ID = 'itemtypes_edit_submit';
    const SUBMIT_ADD_ELEMENT_ID = 'itemtypes_add_submit';
    
    // input names
    const ELEMENTS_INPUT_NAME = 'elements';
    const ELEMENTS_TO_ADD_INPUT_NAME = 'elements-to-add';
    const NEW_ELEMENTS_INPUT_NAME = 'new-elements';
    
    private $_itemType;  // the item type for the form
    
    /* 
       An info array for each item type element in the item type
       each elementInfo contains the following keys:
       
       'element' => the item type element object
       'temp_id' => the temporary form element id for 
                    item type elements that have not yet been added to the item type
    */
    private $_elementInfos; 
    
    public function init()
    {
        parent::init();
        $this->setAttrib('id', self::FORM_ID);
    }
    
    public function setItemType($itemType) 
    {
        $this->_itemType = $itemType;                
        $this->_initElements();
    } 
    
    public function getElementInfos()
    {
        return $this->_elementInfos;
    }
        
    public function saveFromPost() 
    {
        if ($_POST) {            
            if (!$this->_itemType) {
                $this->_itemType = new ItemType;
            }
            
            // get the item type element infos from post
            $this->_elementInfos = $this->_getElementInfosFromPost();
            
            // make sure that there are no duplicates in item type elements            
            $this->_checkForDuplicateElements();
            
            // remove old item type elements from post
            $this->_removeElementsFromItemType();
                        
            // add elements to the item type
            $this->_addElementsToItemType();
        
            // set the name and description of the item type
            $this->_itemType->name = $this->getValue(self::NAME_ELEMENT_ID);
            $this->_itemType->description = $this->getValue(self::DESCRIPTION_ELEMENT_ID);
            
            // save the item type
            if ($this->_itemType->save()) {            
                // reorder the item type's elements
                $this->_reorderItemTypeElements();
            }
        }
        
        return $this->_itemType;
    }
    
    private function _addElementsToItemType()
    {
        $elements = array();
        foreach($this->_elementInfos as $elementInfo) {
            $elements[] = $elementInfo['element'];
        }

        $this->_itemType->addElements($elements);
    }
    
    private function _reorderItemTypeElements()
    {
        $elementOrders = array();
        foreach($this->_elementInfos as $elementInfo) {
            $elementOrders[$elementInfo['element']['id']] = $elementInfo['order'];
        }
        $this->_itemType->reorderElements($elementOrders);
    }
    
    private function _initElements()
    {           
        // set the item type name and description
        $itemTypeName = '';
        $itemTypeDescription = '';
        if ($this->_itemType) {
             $itemTypeName  = $this->_itemType->name;
             $itemTypeDescription  = $this->_itemType->description;
        }
        
        // set the default element infos
        $this->_elementInfos = array();
        if ($this->_itemType) {
            $elementOrder = 1;
            foreach($this->_itemType->Elements as $element) {
                $elementInfo = array(
                    'element' => $element,
                    'temp_id' => null,
                    'order' => $elementOrder,
                );
                $this->_elementInfos[] = $elementInfo;
                $elementOrder++;
            }
        }
         
        $this->clearElements();
        
        $this->addElement('text', self::NAME_ELEMENT_ID,
            array(
                'label' => __('Name'),
                'description' => __('The name of the item type.'),
                'required' => true,
                'value' => $itemTypeName,
            )
        );
        
        $this->addElement('textarea', self::DESCRIPTION_ELEMENT_ID,
            array(
                'label' => __('Description'),
                'description' => __('The description of the item type.'),
                'value' => $itemTypeDescription,
                'cols' => 50,
                'rows' => 5,
            )
        );
        
        $this->addElement('hidden', self::REMOVE_HIDDEN_ELEMENT_ID, array('value' => ''));
        $this->addElement('sessionCsrfToken', 'csrf_token');
    }
    
    private function _checkForDuplicateElements()
    {
        // Check for duplicate elements and throw an exception if a duplicate is found
        $elementIds = array();
        $elementNames = array();
        foreach($this->_elementInfos as $elementInfo) {
            $element = $elementInfo['element'];
            
            // prevent duplicate item type element ids
            if ($element->id) {
                if (in_array($element->id, $elementIds)) {
                    throw new Omeka_Validate_Exception(__('The item type cannot have more than one "%s" element.', $element->name));
                } else {
                    $elementIds[] = $element->id;
                }
            }

            // prevent duplicate item type element names
            if ($element->name) {
                if (in_array($element->name, $elementNames)) {
                    throw new Omeka_Validate_Exception(__('The item type cannot have more than one "%s" element.', $element->name));
                } else {
                    $elementNames[] = trim($element->name);
                }
            }
        }
    }
    
    private function _removeElementsFromItemType()
    {        
        $elementTable = get_db()->getTable('Element');
        // get the item type element ids to remove from the post and remove those elements from the item type
        $elementIds = explode(',', $this->getValue(self::REMOVE_HIDDEN_ELEMENT_ID));            
        foreach($elementIds as $elementId) {
            $elementId = intval(trim($elementId));
            if ($elementId) {
                if ($element = $elementTable->find($elementId)) {
                    $this->_itemType->removeElement($element);        
                }
            }
        }    
    }
    
    // get the elements to save from the post
    private function _getElementInfosFromPost()
    {
        $elementTable = get_db()->getTable('Element');
        $elementInfos = array();

        if (isset($_POST[self::ELEMENTS_INPUT_NAME])) {
            $currentElements = $_POST[self::ELEMENTS_INPUT_NAME];
            foreach ($currentElements as $elementId => $info) {
                $elementInfos[] = array(
                    'element' => $elementTable->find($elementId),
                    'temp_id' => null,
                    'order' => $info['order']
                );
            }
        }

        if (isset($_POST[self::ELEMENTS_TO_ADD_INPUT_NAME])) {
            $elementsToAdd = $_POST[self::ELEMENTS_TO_ADD_INPUT_NAME];
            foreach ($elementsToAdd as $tempId => $info) {
                if (empty($info['id'])) {
                    continue;
                }

                $elementInfos[] = array(
                    'element' => $elementTable->find($info['id']),
                    'temp_id' => $tempId,
                    'order' => $info['order']
                );
            }
        }

        if (isset($_POST[self::NEW_ELEMENTS_INPUT_NAME])) {
            $newElements = $_POST[self::NEW_ELEMENTS_INPUT_NAME];
            foreach ($newElements as $tempId => $info) {
                if (empty($info['name'])) {
                    continue;
                }

                $element = new Element;
                $element->setElementSet(ElementSet::ITEM_TYPE_NAME);
                $element->setName($info['name']);
                $element->setDescription($info['description']);
                $element->order = null;
                                
                $elementInfos[] = array(
                    'element' => $element,
                    'temp_id' => $tempId,
                    'order' => $info['order']
                );
            }
        }

        return $elementInfos;
    }
}
