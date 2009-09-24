<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ItemTypeTable.php';
require_once 'Orderable.php';
require_once 'ItemTypesElements.php';

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemType extends Omeka_Record { 
    
    public $name;
    public $description = '';

    protected $_related = array('Elements' => 'getElements', 
                                'Items'=>'getItems',
                                'ItemTypesElements'=>'loadOrderedChildren');
    
    private $_elementsToSave = array();
    
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
    
    protected function getElements()
    {
        return $this->getTable('Element')->findByItemType($this->id);
    }
    
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
     **/
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Item type name must not be blank.');
        }
        
        if (!$this->fieldIsUnique('name')) {
            $this->addError('name', 'That name has already been used for a different Type.');
        }
    }
    
    /**
     * Delete all the ItemTypesElements joins
     *
     * @return void
     **/
    protected function _delete()
    {
        $tm_objs = $this->getDb()->getTable('ItemTypesElements')->findBySql('item_type_id = ?', array( (int) $this->id));
        
        foreach ($tm_objs as $tm) {
            $tm->delete();
        }
    }
    
    /**
     * Whenever we save the item-type form, reorder the elements based on the keys given in the post.
     * 
     * @param string
     * @return void
     **/
    protected function beforeSaveForm($post)
    {
        if ($this->exists()) {
            $this->_reorderElementsFromPost($post);
        }
    }
    
    /**
     * Save Element records that are associated with this Item Type.
     * 
     * @internal Duplication with ElementSet::afterSave().  Could resolve in 
     * future by refactoring into a mixin that handles record dependencies.
     */
    protected function afterSave()
    {
        foreach ($this->_elementsToSave as $key => $element) {
            $element->forceSave();
            $this->addElementById($element->id);
            unset($this->_elementsToSave[$key]);
        }
    }
    
    /**
     * Validate the elements to ensure saveability-ness.
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
     * @param string
     * @return void
     **/
    private function _reorderElementsFromPost(&$post)
    {
        $elementPostArray = $post['Elements'];
        
        if (!array_key_exists('order', current($elementPostArray))) {
            throw new Omeka_Record_Exception('Form was submitted in an invalid format!');
        }
        
        // This is how we sort the multi-dimensional array based on the element_order.
        $ordering = pluck('order', $elementPostArray);
        $joinRecordArray = $this->ItemTypesElements;        
        // This is essentially voodoo magic.
        array_multisort($ordering, SORT_ASC, SORT_NUMERIC, $joinRecordArray);
        
        $i = 0;
        foreach ($joinRecordArray as $key => $joinRecord) {
            $joinRecord->order = ++$i;
            $joinRecord->forceSave();
        }
    }
    
    /**
     * Add a set of elements to the Item Type.
     * @param array $elementInfo Array of metadata where each entry corresponds
     * to a new element to add to the item type.  
     * @uses Element::setArray() For details on the format for passing metadata
     * through $elementInfo.
     */    
    public function addElements($elementInfo = array())
    {
        foreach ($elementInfo as $elementMetadata) {
            $record = new Element;
            $record->setArray($elementMetadata);
            $record->setElementSet(ELEMENT_SET_ITEM_TYPE);
            $record->setRecordType('Item');
            $this->_elementsToSave[] = $record;
        }
    }
    
    /**
     * Adds a new element to the item type by the id of the element
     * 
     * @param string Id of the element
     * @return void
     *
     **/
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
     * Remove a single Element from this item type.
     * 
     * @param string
     * @return void
     **/
    public function removeElement($elementId)
    {
        if (!$this->exists()) {
            throw new Omeka_Record_Exception('Cannot remove elements from an item type that is not persistent in the database!');
        }
        
        // Find the join record and delete it.
        $iteJoin = $this->getTable('ItemTypesElements')->findBySql('ite.element_id = ? AND ite.item_type_id = ?', array($elementId, $this->id), true);
    
        if (!$iteJoin) {
            throw new Omeka_Record_Exception('Item type does not contain an element with the ID = "' . $elementId . '"!');
        }
        
        $iteJoin->delete();
        
        // Deleting one of the joins throws the whole thing out of whack, so we need to reset the ordering.
        $this->reorderChildren();
    }
    
    public function hasElement($elementId) { 
        $db = $this->getDb();
        
        $iteJoin = $this->getTable('ItemTypesElements')->findBySql('ite.element_id = ? AND ite.item_type_id = ?',
                                    array($elementId, $this->id),
                                    true);
        return (boolean) $iteJoin;
    }
    
    public function totalItems()
    {
        // This will query the ItemTable for a count of all items associated with 
        // the item type
        return $this->getDb()->getTable('Item')->count(array('type' => $this->id));
    }
    
    static public function getItemTypeElementSet()
    {
        // Element should belong to the 'Item Type' element set.
        return get_db()->getTable('ElementSet')->findBySql('name = ?', array(ELEMENT_SET_ITEM_TYPE), true);
    }    
}