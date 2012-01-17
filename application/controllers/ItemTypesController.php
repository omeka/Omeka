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
class ItemTypesController extends Omeka_Controller_Action
{
    const ELEMENTS_TO_REMOVE = 'elements-to-remove';

    const CURRENT_ELEMENT_ORDER_PREFIX = 'element-order-';

    const ADD_NEW_ELEMENT_NAME_PREFIX = 'add-new-element-name-';
    const ADD_NEW_ELEMENT_DATA_TYPE_ID_PREFIX = 'add-new-element-data-type-id-';
    const ADD_NEW_ELEMENT_DESCRIPTION_PREFIX = 'add-new-element-description-';
    const ADD_NEW_ELEMENT_ORDER_PREFIX = 'add-new-element-order-';

    const ADD_EXISTING_ELEMENT_ID_PREFIX = 'add-existing-element-id-';
    const ADD_EXISTING_ELEMENT_ORDER_PREFIX = 'add-existing-element-order-';

    public function init()
    {
        $this->_modelClass = 'ItemType';
    }

    protected function _getDeleteConfirmMessage($record)
    {
        return __('This will delete the item type but will not delete the '
             . 'elements assigned to the item type. Items that are assigned to '
             . 'this item type will lose all metadata that is specific to the '
             . 'item type.');
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
                $this->flashSuccess(__('The item type "%s" was successfully added!  You may now add elements to your new item type.', $itemType->name));
                $this->_redirect("item-types/edit/{$itemType->id}");
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        }
        $this->view->assign(array('itemtype' => $itemType));
    }

    public function editAction()
    {
        $itemType = $this->findById();


        $elementsToSave = array(); // set the default elements to save
        $elementsToAdd = array(); // sets the defaul elements to add
        $elementsToAddTempIds = array(); // set the default elements to add temporary ids
        $elementsToAddIsNew = array(); // set the default elements to add is new
        $elementsToRemove = array();  // sets the default elements to remove

        // set the default item type element order
        $elementsOrder = array();
        if ($elementCount = count($itemType->Elements)) {
           $elementsOrder = range(1, $elementCount);
        }

        try {
            if ($_POST) {
                $this->_extractElementDataFromPost($_POST, $elementsToRemove, $elementsToSave, $elementsToAdd, $elementsToAddTempIds, $elementsToAddIsNew, $elementsOrder);
                $this->_checkForDuplicateElements($elementsToSave);
                $itemType->removeElements($elementsToRemove);
                $itemType->addElements($elementsToSave);
            }

            if ($itemType->saveForm($_POST)) {
                $itemType->reorderElements($elementsOrder);
                $this->flashSuccess(__('The item type "%s" was successfully changed!', $itemType->name));
                $this->redirect->goto('show', null, null, array('id'=>$itemType->id));
            }
        } catch (Omeka_Validator_Exception $e) {
            $this->flashValidationErrors($e);
        }
        $this->view->assign(array('itemtype' => $itemType,
                                  'elementsToAdd' => $elementsToAdd,
                                  'elementsToAddTempIds' => $elementsToAddTempIds,
                                  'elementsToAddIsNew' => $elementsToAddIsNew,
                                  'elementsOrder' => $elementsOrder));
    }

    private function _checkForDuplicateElements(&$elementsToSave)
    {
        // Make sure their are no duplicate elements
        $uniqueElementsToSaveIds = array();
        $uniqueElementsToSaveNames = array();
        foreach($elementsToSave as $elementToSave) {
            if ($elementToSave->id) {
                if (in_array($elementToSave->id, $uniqueElementsToSaveIds)) {
                    throw new Omeka_Validator_Exception(__('The item type cannot have more than one "%s" element.', $elementToSave->name));
                } else {
                    $uniqueElementsToSaveIds[] = $element->id;
                }
            }

            if ($elementToSave->name) {
                if (in_array($elementToSave->name, $uniqueElementsToSaveNames)) {
                    throw new Omeka_Validator_Exception(__('The item type cannot have more than one "%s" element.', $elementToSave->name));
                } else {
                    $uniqueElementsToSaveNames[] = trim($elementToSave->name);
                }
            }
        }
    }

    // get the elements to save from the post and remove all element related post data
    private function _extractElementDataFromPost(&$post, &$elementsToRemove, &$elementsToSave, &$elementsToAdd, &$elementsToAddTempIds, &$elementsToAddIsNew, &$elementsOrder)
    {
        $elementsToSave = array();
        $elementsToAdd = array();
        $elementsToAddTempIds = array();
        $elementsOrder = array();

        foreach($post as $key=>$value) {

            $clearKeysFromPost = array();
            $element = null;
            if (preg_match('/^' . self::CURRENT_ELEMENT_ORDER_PREFIX  . '/', $key)) {

                // get the old element (but do not save it yet)
                $elementId = array_pop(explode('-', $key));
                $element = $this->getDb()->getTable('Element')->find($elementId);
                if ($element->order == 0) {
                    $element->order = null;
                }
                $clearKeysFromPost[] = $key;

                $elementsOrder[] = $post[self::CURRENT_ELEMENT_ORDER_PREFIX . $elementId];

            } else if (preg_match('/^' . self::ADD_NEW_ELEMENT_NAME_PREFIX  . '/', $key)) {

                // construct a new element to add (but do not save it yet)
                $elementTempId = array_pop(explode('-', $key));
                $elementName =  $value;
                $elementDescription = $post[self::ADD_NEW_ELEMENT_DESCRIPTION_PREFIX. $elementTempId];
                $elementDataTypeId = $post[self::ADD_NEW_ELEMENT_DATA_TYPE_ID_PREFIX . $elementTempId];

                $element = new Element;
                $element->setElementSet(ELEMENT_SET_ITEM_TYPE);
                $element->setRecordType('Item');
                $element->setName($elementName);
                $element->setDescription($elementDescription);
                $element->data_type_id = $elementDataTypeId;
                $element->order = null;

                $clearKeysFromPost[] = $key;
                $clearKeysFromPost[] = self::ADD_NEW_ELEMENT_DESCRIPTION_PREFIX . $elementTempId;
                $clearKeysFromPost[] = self::ADD_NEW_ELEMENT_DATA_TYPE_ID_PREFIX . $elementTempId;
                $clearKeysFromPost[] = self::ADD_NEW_ELEMENT_ORDER_PREFIX . $elementTempId;

                $elementsToAdd[] = $element;
                $elementsToAddTempIds[] = $elementTempId;
                $elementsToAddIsNew[] = true;

                $elementsOrder[] = $post[self::ADD_NEW_ELEMENT_ORDER_PREFIX . $elementTempId];

            } else if (preg_match('/^' . self::ADD_EXISTING_ELEMENT_ID_PREFIX  . '/', $key)) {

                // construct an existing element to add (but do not save it yet)
                $elementTempId = array_pop(explode('-', $key));
                $elementId = $post[self::ADD_EXISTING_ELEMENT_ID_PREFIX. $elementTempId];
                $element = $this->getDb()->getTable('Element')->find($elementId);

                if ($element) {
                    if ($element->order == 0) {
                        $element->order = null;
                    }
                } else {
                    $element = new Element;
                    $element->setElementSet(ELEMENT_SET_ITEM_TYPE);
                    $element->setRecordType('Item');
                    $element->order = null;
                }

                $clearKeysFromPost[] = $key;
                $clearKeysFromPost[] = self::ADD_EXISTING_ELEMENT_ORDER_PREFIX . $elementTempId;

                $elementsToAdd[] = $element;
                $elementsToAddTempIds[] = $elementTempId;
                $elementsToAddIsNew[] = false;

                $elementsOrder[] = $post[self::ADD_EXISTING_ELEMENT_ORDER_PREFIX . $elementTempId];
            }

            // clear the keys of the post vars related to the element
            foreach ($clearKeysFromPost as $clearKey) {
                // clear the post data related to the elements
                $this->_clearPostVar($clearKey);
            }

            // Add the element to save if it exists
            if ($element) {
                $elementsToSave[] = $element;
            }
        }

        $elementsToRemove = $this->_getElementsToRemoveFromPost($post);
    }

    private function _getElementsToRemoveFromPost(&$post)
    {
        // get the elements to delete from the post
        $elementsToRemove = array();
        $elementIdsToRemove = array();
        $elementIds = explode(',', $post[self::ELEMENTS_TO_REMOVE]);
        foreach($elementIds as $elementId) {
            $elementId = (int)trim($elementId);
            if ($elementId && !in_array($elementId, $elementIdsToRemove)) {
                $elementToRemove = $this->getDb()->getTable('Element')->find($elementId);
                if ($elementToRemove) {
                   $elementsToRemove[] = $elementToRemove;
                   $elementIdsToRemove[] = $elementId;
                }
            }
        }

        // remove the element to delete data from the post
        $this->_clearPostVar(self::ELEMENTS_TO_REMOVE);

        return $elementsToRemove;
    }

    public function addNewElementAction()
    {
        $elementCount = (int)$this->_getParam('elementCount');

        if ($this->_getParam('from_post') == 'true') {
            $elementTempId = $this->_getParam('elementTempId');
            $elementName = $this->_getParam('elementName');
            $elementDescription = $this->_getParam('elementDescription');
            $elementDataTypeId = $this->_getParam('elementDataTypeId');
            $elementOrder = $this->_getParam('elementOrder');
        } else {
            $elementTempId = '' . time();
            $elementName = '';
            $elementDescription = '';
            $elementDataTypeId = '0';
            $elementOrder = $elementCount + 1;
        }

        $this->view->assign(array('elementTempId' => $elementTempId,
                                  'elementName' => $elementName,
                                  'elementDescription' => $elementDescription,
                                  'elementDataTypeId' => $elementDataTypeId,
                                  'elementOrder' => $elementOrder,
                                  'addNewElementNamePrefix' => self::ADD_NEW_ELEMENT_NAME_PREFIX,
                                  'addNewElementDataTypeIdPrefix' => self::ADD_NEW_ELEMENT_DATA_TYPE_ID_PREFIX,
                                  'addNewElementDescriptionPrefix' => self::ADD_NEW_ELEMENT_DESCRIPTION_PREFIX,
                                  'addNewElementOrderPrefix' => self::ADD_NEW_ELEMENT_ORDER_PREFIX
                                   ));
    }

    public function addExistingElementAction()
    {
        $elementCount = (int)$this->_getParam('elementCount');

        if ($this->_getParam('from_post') == 'true') {
            $elementTempId = $this->_getParam('elementTempId');
            $elementId = $this->_getParam('elementId');
            $element = $this->getDb()->getTable('Element')->find($elementId);
            if ($element) {
                $elementDescription = $element->description;
                $elementDataTypeName = $element->getDataType()->name;
            }
            $elementOrder = $this->_getParam('elementOrder');
        } else {
            $elementTempId = '' . time();
            $elementId = '';
            $elementDescription = '';
            $elementDataTypeName = '';
            $elementOrder = $elementCount + 1;
        }

        $this->view->assign(array('elementTempId' => $elementTempId,
                                  'elementId' => $elementId,
                                  'elementDescription' => $elementDescription,
                                  'elementDataTypeName' => $elementDataTypeName,
                                  'elementOrder' => $elementOrder,
                                  'addExistingElementIdPrefix' => self::ADD_EXISTING_ELEMENT_ID_PREFIX,
                                  'addExistingElementOrderPrefix' => self::ADD_EXISTING_ELEMENT_ORDER_PREFIX
                                  ));
    }

    public function changeExistingElementAction()
    {
        $elementId = $this->_getParam('elementId');
        $elementTempId = $this->_getParam('elementTempId');

        $element = $this->getTable('Element')->find($elementId);


        $elementDescription = '';
        $elementDataTypeName = '';
        if ($element) {
            $elementDescription = $element->description;
            $elementDataTypeName = $element->getDataType()->name;
        }

        $data = array();
        $data['elementDescription'] = $elementDescription;
        $data['elementDataTypeName'] = $elementDataTypeName;

        $this->_helper->json($data);
    }

    private function _clearPostVar($postKey)
    {
        // clear the post data for the the specified post key
        $this->_setParam($postKey, null);
        $post[$postKey] = null;
        unset($post[$postKey]);
    }

    public function elementListAction()
    {
        $itemTypeId = $this->_getParam('item-type-id');
        $itemType = $this->findById($itemTypeId);
        $this->view->itemtype = $itemType;
        $this->view->elements = $itemType->Elements;
    }
}
