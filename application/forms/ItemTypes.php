<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Item types form.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2012
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
    const DELETE_ELEMENT_ID = 'itemtypes_delete';
    const SUBMIT_ADD_ELEMENT_ID = 'itemtypes_add_submit';
    
    // prefixes
    const CURRENT_ELEMENT_ORDER_PREFIX = 'element-order-';
    const ADD_NEW_ELEMENT_NAME_PREFIX = 'add-new-element-name-';
    const ADD_NEW_ELEMENT_DESCRIPTION_PREFIX = 'add-new-element-description-';
    const ADD_NEW_ELEMENT_ORDER_PREFIX = 'add-new-element-order-';
    const ADD_EXISTING_ELEMENT_ID_PREFIX = 'add-existing-element-id-';
    const ADD_EXISTING_ELEMENT_ORDER_PREFIX = 'add-existing-element-order-';
    
    private $_itemType;
    private $_elementsOrder;
    
    private $_elementsToSave; // the item type elements to save
    
    private $_elementsToAdd; // the item type elements to add
    private $_elementsToAddTempIds; // the item type elements to add temporary ids
    private $_elementsToAddIsNew; // the item type elements to add is new
    
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
    
    public function getElementsToAdd()
    {
        return $this->_elementsToAdd;
    }
    
    public function getElementsToAddTempIds()
    {
        return $this->_elementsToAddTempIds;
    }
    
    public function getElementsToAddIsNew()
    {
        return $this->_elementsToAddIsNew;
    }
    
    public function getElementsOrder() 
    {
        return $this->_elementsOrder;
    }
        
    public function saveFromPost() 
    {
        if ($_POST) {            
            if (!$this->_itemType) {
                $this->_itemType = new ItemType;
            }
            
            $elementsToSave = $this->_getElementsToSaveFromPost();            
            $this->_checkForDuplicateElements($elementsToSave);
            $this->_removeElementsFromPost();
            $this->_itemType->addElements($elementsToSave);
        
            $this->_itemType->name = $this->getValue(self::NAME_ELEMENT_ID);
            $this->_itemType->description = $this->getValue(self::DESCRIPTION_ELEMENT_ID);
                        
            if ($this->_itemType->save()) {            
                $this->_itemType->reorderElements($this->_elementsOrder);
            }
        }
        
        return $this->_itemType;
    }
    
    private function _initElements()
    {   
        $this->_elementsToAdd = array();
        $this->_elementsToAddTempIds = array(); 
        $this->_elementsToAddIsNew = array(); 
        
        // set the item type name and description
        $itemTypeName = '';
        $itemTypeDescription = '';
        if ($this->_itemType) {
             $itemTypeName  = $this->_itemType->name;
             $itemTypeDescription  = $this->_itemType->description;
        }
        
        // set the default item type element order
        $this->_elementsOrder = array();
        if ($this->_itemType) {
            if ($elementCount = count($this->_itemType->Elements)) {
                $this->_elementsOrder = range(1, $elementCount);
            }
        }
         
        $this->clearElements();
        
        $this->addElement('text', self::NAME_ELEMENT_ID,
            array(
                'label' => __('Name'),
                'description' => __('The name of the item type.'),
                'required' => true,
                'value' => $itemTypeName,
                'class' => 'textinput',
                'decorators' =>  array(
                            'ViewHelper',
                            array('HtmlTag', array('tag' => 'dd')),
                            array('Label', array('tag' => 'dt', 'class' => 'two columns alpha')),                           
                            'Errors',)
            )
        );                
        
        $this->addElement('textarea', self::DESCRIPTION_ELEMENT_ID,
            array(
                'label' => __('Description'),
                'description' => __('The description of the item type.'),
                'value' => $itemTypeDescription,
                'cols' => 50,
                'rows' => 13,
                'class' => 'textinput',
                'decorators' =>  array(
                            'ViewHelper',
                            array('HtmlTag', array('tag' => 'dd')),
                            array('Label', array('tag' => 'dt', 'class' => 'two columns alpha')),                           
                            'Errors',)
            )
        );        
        
        $this->addElement('hidden', self::REMOVE_HIDDEN_ELEMENT_ID, array('value' => ''));
        
        $this->addElement('submit', self::SUBMIT_ADD_ELEMENT_ID, array(
            'label' => __('Add Item Type'),
            'class' => 'big red button',
            'decorators' =>  array(
                        'ViewHelper',
                        'Errors',)
        ));
                
        $this->addElement('submit', self::SUBMIT_EDIT_ELEMENT_ID, array(
            'label' => __('Save Changes'),
            'class' => 'big green button',
            'decorators' =>  array(
                        'ViewHelper',
                        'Errors',)
        ));
        
        $this->addElement('submit', self::DELETE_ELEMENT_ID, array(
            'label' => __('Delete this Item Type'),
            'class' => 'big red button',
            'decorators' =>  array(
                        'ViewHelper',
                        'Errors',)
        ));
    }
    
    private function _checkForDuplicateElements($elements)
    {
        // Make sure their are no duplicate elements
        $elementIds = array();
        $elementNames = array();
        foreach($elements as $element) {
            if ($element->id) {
                if (in_array($element->id, $elementIds)) {
                    throw new Omeka_Validator_Exception(__('The item type cannot have more than one "%s" element.', $elementToSave->name));
                } else {
                    $elementIds[] = $element->id;
                }
            }

            if ($element->name) {
                if (in_array($element->name, $elementNames)) {
                    throw new Omeka_Validator_Exception(__('The item type cannot have more than one "%s" element.', $elementToSave->name));
                } else {
                    $elementNames[] = trim($element->name);
                }
            }
        }
    }
    
    private function _removeElementsFromPost()
    {        
        $elementTable = get_db()->getTable('Element');
        
        // get the elements to delete from the post
        $elements = array();
        $elementIds = array();
        $pElementIds = explode(',', $this->getValue(self::REMOVE_HIDDEN_ELEMENT_ID));
                
        foreach($pElementIds as $elementId) {
            $elementId = intval(trim($elementId));
            if ($elementId && !in_array($elementId, $elementIds)) {
                if ($elementToRemove = $elementTable->find($elementId)) {
                   $elements[] = $elementToRemove;
                   $elementIds[] = $elementId;
                }
            }
        }
    
        $this->_itemType->removeElements($elements);        
    }
    
    // get the elements to save from the post
    private function _getElementsToSaveFromPost()
    {
        $elementTable = get_db()->getTable('Element');
        
        $elementsToSave = array();
        $this->_elementsOrder = array();

        $this->_elementsToAdd = array();
        $this->_elementsToAddTempIds = array();
        $this->_elementsToAddIsNew = array();
                        
        foreach($_POST as $key => $value) {

            $element = null;
            $elementOrder = null;
            if (preg_match('/^' . self::CURRENT_ELEMENT_ORDER_PREFIX  . '/', $key)) {

                // get the old element (but do not save it yet)
                $elementIdParts = explode('-', $key);
                $elementId = array_pop($elementIdParts);
                $element = $elementTable->find($elementId);
                $elementOrder = $_POST[self::CURRENT_ELEMENT_ORDER_PREFIX . $elementId];

            } else if (preg_match('/^' . self::ADD_NEW_ELEMENT_NAME_PREFIX  . '/', $key)) {

                // construct a new element to add (but do not save it yet)
                $elementTempIdParts = explode('-', $key);
                $elementTempId = array_pop($elementTempIdParts);
                $element = new Element;
                $element->setElementSet(ELEMENT_SET_ITEM_TYPE);
                $element->setName($value);
                $element->setDescription($_POST[self::ADD_NEW_ELEMENT_DESCRIPTION_PREFIX . $elementTempId]);
                $element->order = null;
                $elementOrder = $_POST[self::ADD_NEW_ELEMENT_ORDER_PREFIX . $elementTempId];
                                
                $this->_elementsToAdd[] = $element;
                $this->_elementsToAddTempIds[] = $elementTempId;
                $this->_elementsToAddIsNew[] = true;
                
            } else if (preg_match('/^' . self::ADD_EXISTING_ELEMENT_ID_PREFIX  . '/', $key)) {

                // construct an existing element to add (but do not save it yet)
                $elementTempIdParts = explode('-', $key);
                $elementTempId = array_pop($elementTempIdParts);
                $elementId = $_POST[self::ADD_EXISTING_ELEMENT_ID_PREFIX . $elementTempId];
                $element = $elementTable->find($elementId);                
                
                if (!$element) {
                    $element = new Element;
                    $element->setElementSet(ELEMENT_SET_ITEM_TYPE);
                    $element->order = null;
                }
                $elementOrder = $_POST[self::ADD_EXISTING_ELEMENT_ORDER_PREFIX . $elementTempId];
                
                
                $this->_elementsToAdd[] = $element;
                $this->_elementsToAddTempIds[] = $elementTempId;
                $this->_elementsToAddIsNew[] = false;
            }

            // Add the element to save
            if ($element) {
                if ($element->order == 0) {
                    $element->order = null;
                }
                $elementsToSave[] = $element;
                $this->_elementsOrder[] = $elementOrder;                
            }
        }        
        
        return $elementsToSave;
    }
}
