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
    
    protected $_elementsToSave;
    
    const DEFAULT_RECORD_TYPE = 'Item';
    const DEFAULT_DATA_TYPE = 'Text';
    
    public function getElements()
    {
        return $this->getTable('Element')->findBySet($this->name);
    }
    
    protected function getDefaultRecordTypeId()
    {
        return $this->getTable('RecordType')->findIdFromName(self::DEFAULT_RECORD_TYPE);
    }
    
    protected function getDefaultDataTypeId()
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
        // By default, new elements will work only for items and be Text fields.
        $defaultRecordType = $this->getDefaultRecordTypeId();
        $defaultDataType = $this->getDefaultDataTypeId();
        
        $order = 1;
        foreach ($elements as $options) {
            
            $obj = $this->_buildElementRecord($options);
            
            // Set defaults for the record_type and data_type
            if (!$obj->record_type_id) {
                $obj->record_type_id = $defaultRecordType;
            }
            
            if (!$obj->data_type_id) {
                $obj->data_type_id = $defaultDataType;
            }
            
            if (!$obj->order) {
                $obj->order = $order;
            }
            
            $this->_elementsToSave[] = $obj;
            $order++;
        }
        // var_dump($this->_elementsToSave);exit;
    }
    
    protected function _buildElementRecord($options)
    {
        if (is_array($options)) {
            $obj = new Element;
            $obj->setArray($options);
            
            if (isset($options['record_type'])) {
                $obj->record_type_id = $this->getTable('RecordType')->findIdFromName($options['record_type']);
            }
            
            if (isset($options['data_type'])) {
                $obj->data_type_id = $this->getTable('DataType')->findIdFromName($options['data_type']);
            }
        } else if ($options instanceof Element) {
            if ($options->exists()) {
                $obj = clone $options;
                $obj->id = null;
            } else {
                $obj = $options;
            }
        } else {
            $obj = new Element;
            $obj->name = $options;
        }
        
        return $obj;        
    }
    
    /**
     * Set some default options when saving element sets (if not given).
     * 
     * @return void
     **/
    public function beforeSave()
    {
        if (empty($this->record_type_id)) {
            $this->record_type_id = $this->getDefaultRecordTypeId();
        }
        
        if (empty($this->data_type_id)) {
            $this->data_type_id = $this->getDefaultDataTypeId();
        }
    }
    
    public function afterSave()
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
}
