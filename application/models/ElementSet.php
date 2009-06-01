<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ElementSetTable.php';
 
/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ElementSet extends Omeka_Record
{
    public $record_type_id;
    public $name;
    public $description;
    
    protected $_elementsToSave = array();
    
    const DEFAULT_RECORD_TYPE = 'Item';
    const DEFAULT_DATA_TYPE = 'Text';
    
    public function getElements()
    {
        return $this->getTable('Element')->findBySet($this->name);
    }
    
    private function _getDefaultRecordTypeId()
    {
        return $this->getTable('RecordType')->findIdFromName(self::DEFAULT_RECORD_TYPE);
    }
    
    private function _getDefaultDataTypeId()
    {
        return $this->getTable('DataType')->findIdFromName(self::DEFAULT_DATA_TYPE);
    }
    
    /**
     * Three syntaxes for accessing this:
     * 
     * @param array
     * @return void
     **/
    public function addElements(array $elements)
    {        
        $order = $this->_getNextElementOrder();
        foreach ($elements as $options) {
            
            $record = $this->_buildElementRecord($options);
            
            if (!$record->order) {
                $record->setOrder($order);
            // If an order was passed, assume it is relative to the other 
            // elements that are being added, and not necessarily the actual 
            // element order for the element set. This will set the order to the 
            // highest order number plus one.
            } else {
                $record->setAutoOrder(true);
                // $record->setOrder = $obj->order + ($order - 1);
            }
            
            $this->_elementsToSave[] = $record;
            $order++;
        }
        // var_dump($this->_elementsToSave);exit;
    }
    
    private function _buildElementRecord($options)
    {
        $obj = new Element;
        $obj->setArray($options);
        return $obj;        
    }
    
    /**
     * Set some default options when saving element sets (if not given).
     * 
     * @return void
     **/
    protected function beforeSave()
    {
        if (empty($this->record_type_id)) {
            $this->record_type_id = $this->_getDefaultRecordTypeId();
        }
        
        if (empty($this->data_type_id)) {
            $this->data_type_id = $this->_getDefaultDataTypeId();
        }
    }
    
    protected function afterSave()
    {
        foreach ($this->_elementsToSave as $obj) {
            $obj->element_set_id = $this->id;
            $obj->forceSave();
        }
    }
    
    /**
     * Deletes all the elements associated with an element set.
     * 
     * @return void
     **/
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
            $this->addError('Name', 'Name of element set must be unique.');
        }
        
        if (empty($this->name)) {
            $this->addError('Name', 'Name of element set must not be empty.');
        }
    }
}
