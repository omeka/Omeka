<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An element set and its metadata.
 * 
 * @package Omeka\Record
 */
class ElementSet extends Omeka_Record_AbstractRecord
{
    public $record_type;
    public $name;
    public $description;
    
    protected $_elementsToSave = array();
    
    /**
     * The name of the item type element set.
     * 
     * This is used wherever it is important to distinguish this special case 
     * element set from others.
     */
    const ITEM_TYPE_NAME = 'Item Type Metadata';
    
    public function getElements()
    {
        return $this->getTable('Element')->findBySet($this->name);
    }
    
    /**
     * Add elements to the element set.
     * 
     * @param array $elements
     */
    public function addElements(array $elements)
    {        
        foreach ($elements as $order => $options) {
            $record = $this->_buildElementRecord($options);
            $this->_elementsToSave[] = $record;
        }
    }
    
    private function _buildElementRecord($options)
    {
        $obj = new Element;
        $obj->setArray($options);
        return $obj;        
    }
    
    protected function afterSave($args)
    {
        $maxOrder = $this->_getNextElementOrder();
        foreach ($this->_elementsToSave as $order => $obj) {
            $obj->element_set_id = $this->id;
            $obj->setOrder($maxOrder + (int)$order);
            $obj->save();
            unset($this->_elementsToSave[$order]);
        }
    }
    
    /**
     * Deletes all the elements associated with an element set.
     * 
     * @return void
     */
    protected function _delete()
    {
        // Delete all elements that belong to this element set.
        $elements = $this->getElements();
        foreach ($elements as $element) {
            $element->delete();
        }
    }
    
    private function _getNextElementOrder()
    {
        $db = $this->getDb();
        $sql = "
        SELECT MAX(`order`) + 1 
        FROM $db->Element e 
        WHERE e.`element_set_id` = ?";
        $nextElementOrder = $db->fetchOne($sql, $this->id);
        // In MySQL, NULL + 1 = NULL.
        if (!$nextElementOrder) {
            $nextElementOrder = 1;
        }
        return $nextElementOrder;
    }
    
    protected function _validate()
    {
        if (!$this->fieldIsUnique('name')) {
            $this->addError('Name', __('Name of element set must be unique.'));
        }
        
        if (empty($this->name)) {
            $this->addError('Name', __('Name of element set must not be empty.'));
        }
    }
}
