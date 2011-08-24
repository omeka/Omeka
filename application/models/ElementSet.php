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
    
    /**
     * Set some default options when saving element sets (if not given).
     * 
     * @return void
     */
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
        $maxOrder = $this->_getNextElementOrder();
        foreach ($this->_elementsToSave as $order => $obj) {
            $obj->element_set_id = $this->id;
            $obj->setOrder($maxOrder + (int)$order);
            $obj->forceSave();
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
