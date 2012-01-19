<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ItemType extends Omeka_Record
{
    const ITEM_TYPE_NAME_MIN_CHARACTERS = 1;
    const ITEM_TYPE_NAME_MAX_CHARACTERS = 255;

    public $name;
    public $description = '';

    protected $_related = array('Elements' => 'getElements',
                                'Items'=>'getItems',
                                'ItemTypesElements'=>'loadOrderedChildren');

    private $_elementsToSave = array();
    private $_elementsToRemove = array();

    protected function _initializeMixins()
    {
        // For future reference, these arguments mean:
        // 1) the current object
        // 2) the name of the model that represents the 'child' objects, otherwise
        // known as the ordered set belonging to this object.
        // 3) the foreign key in that model that corresponds to the primary key in this model
        // 4) The name for the part of the form that contains info about how
        // these child objects are ordered. The post for this model's form should
        // always contain the 'Elements' key with a subkey called 'order', so that
        // the array looks something like this: $_POST['Elements'][0]['order'] = 1, etc.
        // NOTE: this has been changed to 'fooobar' in order to circumvent using
        // Orderable::afterSaveForm() in favor of ItemType::_reorderElementsFromPost().
        $this->_mixins[] = new Orderable($this, 'ItemTypesElements', 'item_type_id', 'fooobar');
    }

    /**
     * Returns an array of element objects associated with this item type.
     *
     * @return array The array of element objects associated with this item type.
     */
    protected function getElements()
    {
        return $this->getTable('Element')->findByItemType($this->id);
    }

    /**
     * Returns an array of item objects that have this item type.
     *
     * @param int $count The maximum number of items to return.
     * @param boolean $recent  Whether or not the items are recent.
     * @return array The items associated with the item type.
     */
    protected function getItems($count = 10, $recent=true)
    {
        $params = array('type'=>$this->id);
        if ($recent) {
            $params['recent'] = true;
        }
        return $this->getTable('Item')->findBy($params, $count);
    }

    /**
     * Current validation rules for Type
     *
     * 1) 'Name' field can't be blank
     * 2) 'Name' field must be unique
     *
     * @return void
     */
    protected function _validate()
    {

        if (strlen($this->name) < self::ITEM_TYPE_NAME_MIN_CHARACTERS || strlen($this->name) > self::ITEM_TYPE_NAME_MAX_CHARACTERS) {
            $this->addError('name', __('The item type name must have between %1$s and %2$s characters.', self::ITEM_TYPE_NAME_MIN_CHARACTERS, self::ITEM_TYPE_NAME_MAX_CHARACTERS) );
        }

        if (!$this->fieldIsUnique('name')) {
            $this->addError('name', __('The item type name must be unique.'));
        }
    }

    /**
     * Filter incoming POST data from ItemType form.
     *
     * @return void
     */
    protected function filterInput($post)
    {
        $options = array('inputNamespace'=>'Omeka_Filter');

        // User form input does not allow superfluous whitespace
        $filters = array('name' => array('StripTags', 'StringTrim'),
                        'description' => array('StringTrim'));

        $filter = new Zend_Filter_Input($filters, null, $post, $options);

        $post = $filter->getUnescaped();

        return $post;
    }

    /**
     * Delete all the ItemTypesElements joins
     *
     * @return void
     */
    protected function _delete()
    {
        $tm_objs = $this->getDb()->getTable('ItemTypesElements')->findBySql('item_type_id = ?', array( (int) $this->id));
        foreach ($tm_objs as $tm) {
            $tm->delete();
        }
    }

    /**
     * Save Element records that are associated with this Item Type.
     *
     * @internal Duplication with ElementSet::afterSave().  Could resolve in
     * future by refactoring into a mixin that handles record dependencies.
     *
     * @return void
     */
    protected function afterSave()
    {
        // remove the elements that need to be removed
        foreach ($this->_elementsToRemove as $key => $element) {
            $this->_removeElement($element);
            unset($this->_elementsToRemove[$key]);
        }

        // add the elements that need to be added
        foreach ($this->_elementsToSave as $key => $element) {
            $element->forceSave();
            $this->addElementById($element->id);
            unset($this->_elementsToSave[$key]);
        }
    }

    /**
     * Validate the elements to ensure saveability-ness.
     *
     * @return void
     */
    protected function afterValidate()
    {
        foreach ($this->_elementsToSave as $key => $element) {
            if (!$element->isValid()) {
                $this->addError("Element #$key", $element->getErrors());
            }
        }
    }

    /**
     * This extracts the ordering for the elements from the form's POST, then uses
     * the given ordering to resort the join records from item_types_elements into
     * a new ordering, which is then saved.
     *
     * @param Array $elementOrderingArray An array of numbers representing
     * @return void
     */
    public function reorderElements($elementOrderingArray)
    {
        $joinRecordArray = $this->loadOrderedChildren();

        if (count($elementOrderingArray) > count($joinRecordArray)) {
            throw new Omeka_Record_Exception(__('There are too many values in the element ordering array.'));
        } else if (count($elementOrderingArray) < count($joinRecordArray)) {
            throw new Omeka_Record_Exception(__('There are too few values in the element ordering array.'));
        }

        // This is essentially voodoo magic.
        array_multisort($elementOrderingArray, SORT_ASC, SORT_NUMERIC, $joinRecordArray);

        $i = 0;
        foreach ($joinRecordArray as $key => $joinRecord) {
            $joinRecord->order = ++$i;
            $joinRecord->forceSave();
        }
    }

    /**
     * Add a set of elements to the Item Type.
     *
     * @param array $elements Either an array of elements
     * or an array of metadata, where each entry corresponds
     * to a new element to add to the item type.  If an element exists with the same id,
     * it will replace the old element with the new element.
     *
     * @uses Element::setArray() For details on the format for passing metadata
     * through $elementInfo.
     *
     * @return void
     */
    public function addElements($elements = array())
    {
        $elementsToSave = array();
        $elementsToSaveIds = array();
        foreach ($elements as $element) {
            $elementToSave = null;
            if (is_array($element)) {
                // the element is an array of element metadata
                $elementToSave = new Element;
                $elementToSave->setArray($element);
                $elementToSave->setElementSet(ELEMENT_SET_ITEM_TYPE);
                $elementToSave->setRecordType('Item');
            } else if ($element instanceof Element) {
                $elementToSave = $element;
                if ($element->id) {
                    $elementsToSaveIds[] = $element->id;
                }
            } else {
                throw new Omeka_Record_Exception(__('Invalid element data. To add elements, you must either pass an element objects or an array of element metadata.'));
            }
            if ($elementToSave) {
                $elementsToSave[] = $elementToSave;
            }
        }

        // check to see if the element already exists in the $this->_elementToSave,
        // and if it does, then replace the old element with the new element
        foreach($this->_elementsToSave as $oldElementToSave) {
            if (!$oldElementToSave->id || !in_array($oldElementToSave->id, $elementsToSaveIds)) {
                $elementsToSave[] = $oldElementToSave;
            }
        }

        // reset the $_elementsToSave
        $this->_elementsToSave = $elementsToSave;
    }

    /**
     * Adds a new element to the item type by the id of the element
     *
     * @param string Id of the element
     * @return void
     *
     */
    public function addElementById($elementId)
    {
        if (!$this->hasElement($elementId)) {
            // Once we have a persistent Element record, build the join record.
            $iteJoin = new ItemTypesElements;
            $iteJoin->element_id = $elementId;
            $iteJoin->item_type_id = $this->id;
            // 'order' should be last by default.
            $iteJoin->order = $this->getChildCount() + 1;
            $iteJoin->forceSave();
        }
    }

    /**
     * Removes an array of Elements from this item type
     * The element will not be removed until the object is saved.
     *
     * @since 1.2
     * @param Array $elements An array of Element objects or element id strings
     * @return void
     */
    public function removeElements($elements)
    {
        foreach($elements as $element) {
            $this->removeElement($element);
        }
    }

    /**
     * Remove a single Element from this item type.
     * The element will not be removed until the object is saved.
     *
     * @param Element|string $element The element object or the element id.
     * @return void
     */
    public function removeElement($element)
    {
        if (!$this->exists()) {
            throw new Omeka_Record_Exception(__('Cannot remove elements from an item type that is not persistent in the database!'));
        }

        if ($element instanceof Element) {
            $elementId = $element->id;
        } else if (is_string($element)) {
            $elementId = $element;
            $element = $this->getTable('Element')->find($elementId);
            if (!$element) {
                throw new Omeka_Record_Exception(__('Cannot find element with ID %s!', $elementId));
            }
        }

        // Remove the element from the elements to save
        $elementsToSave = array();
        foreach($this->_elementsToSave as $elementToSave) {
            if ($elementToSave->id != $elementId) {
                $elementsToSave[] = $elementToSave;
            }
        }
        $this->_elementsToSave = $elementsToSave;

        // Reset the elements to remove
        $elementsToRemove = array();
        $hasElement = false;
        foreach($this->_elementsToRemove as $elementToRemove) {
            if ($elementToRemove->id == $elementId) {
               $hasElement = true;
               break;
            }
        }
        if (!$hasElement) {
            if ($element) {
                $this->_elementsToRemove[] = $element;
            }
        }
    }

     /**
     * Removes a single Element from this item type.  It removes it immediately.
     *
     * @param Element|string $element
     * @return void
     */
    private function _removeElement($element)
    {
        $elementId = $element->id;

        // Find the join record and delete it.
        $iteJoin = $this->getTable('ItemTypesElements')->findBySql('ite.element_id = ? AND ite.item_type_id = ?', array($elementId, $this->id), true);

        if (!$iteJoin) {
            throw new Omeka_Record_Exception(__('Item type does not contain an element with the ID %s!', $elementId));
        }
        $iteJoin->delete();

        // Deleting one of the joins throws the whole thing out of whack, so we need to reset the ordering.
        $this->reorderChildren();
    }

     /**
     * Determines whether a saved version of the item type has an element.
     * It does not correctly determine the presence of elements that were added or
     * removed without saving the item type object.
     *
     * @param Element|string $element  The element object or the element id.
     * @return boolean
     */
    public function hasElement($element)
    {
        if ($element instanceof Element) {
            $elementId = $element->id;
        } else if (is_string($element) || is_integer($element)) {
            $elementId = (string) $element;
        } else {
            throw new Omeka_Record_Exception(__('Invalid parameter. The hasElement function requires either an element object or an element id to determine if an item type has an element.'));
        }
        $db = $this->getDb();
        $iteJoin = $this->getTable('ItemTypesElements')->findBySql('ite.element_id = ? AND ite.item_type_id = ?',
                                    array($elementId, $this->id),
                                    true);
        return (boolean) $iteJoin;
    }

    /**
     * Determines the total number of items that have this item type.
     *
     * @return int The total number of items that have this item type.
     */
    public function totalItems()
    {
        // This will query the ItemTable for a count of all items associated with
        // the item type
        return $this->getDb()->getTable('Item')->count(array('type' => $this->id));
    }


    /**
     * Returns the 'Item Type' element set.
     *
     * @return ElementSet
     */
    static public function getItemTypeElementSet()
    {
        // Element should belong to the 'Item Type' element set.
        return get_db()->getTable('ElementSet')->findBySql('name = ?', array(ELEMENT_SET_ITEM_TYPE), true);
    }
}
