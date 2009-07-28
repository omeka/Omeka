<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ElementTable.php';
require_once 'RecordType.php';
require_once 'DataType.php';
require_once 'ElementSet.php';
 
/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Element extends Omeka_Record
{
    public $record_type_id;
    public $data_type_id;
    public $element_set_id;
    public $order;
    public $name = '';
    public $description = '';

    const DEFAULT_RECORD_TYPE = 'Item';
    const DEFAULT_DATA_TYPE = 'Text';
    
    /**
     * Set the record type for the element (Item, File, All, etc.).
     * @param string $recordTypeName
     */
    public function setRecordType($recordTypeName)
    {
        $this->record_type_id = $this->_getRecordTypeId($recordTypeName);
    }
    
    /**
     * Set the data type for the element (Text, Tiny Text, etc.).
     * @param string $dataTypeName
     */
    public function setDataType($dataTypeName)
    {
        $this->data_type_id = $this->_getDataTypeId($dataTypeName);
    }
    
    /**
     * Set the element set for the element. 
     * @param string $elementSetName
     */
    public function setElementSet($elementSetName)
    {
        $this->element_set_id = $this->_getElementSetId($elementSetName);
    }
    
    /**
     * Return the ElementSet objection for this element.
     * 
     * @return ElementSet
     */
    public function getElementSet()
    {
        if(($setId = $this->element_set_id)) {
            return $this->getDb()->getTable('ElementSet')->find($setId);
        }
        else {
            return null;
        }
    }
    
    /**
     * Set the order of the element within its element set.
     * @param integer $order
     */
    public function setOrder($order)
    {
        $this->order = (int)$order;
    }
        
    /**
     * Set the name of the element.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = trim($name);
    }
    
    /**
     * Set the description for the element.
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = (string)trim($description);
    }
    
    /**
     * @param array|string $data If string, it's the name of the element.  
     * Otherwise, array of metadata for the element.  May contain the following
     * keys in the array:
     * <ul>
     *  <li>name</li>
     *  <li>description</li>
     *  <li>order</li>
     *  <li>record_type_id</li>
     *  <li>data_type_id</li>
     *  <li>element_set_id</li>
     *  <li>record_type</li>
     *  <li>data_type</li>
     *  <li>element_set</li>
     * </ul>
     */
    public function setArray($data)
    {
        if (is_string($data)) {
            $this->setName($data);
        } else {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'record_type':
                        $this->setRecordType($value);
                        break;
                    case 'data_type':
                        $this->setDataType($value);
                        break;
                    case 'order':
                        $this->setOrder($value);
                        break;
                    case 'element_set':
                        $this->setElementSet($value);
                        break;
                    case 'name':
                        $this->setName($value);
                        break;
                    case 'description':
                        $this->setDescription($value);
                        break;
                    default:
                        $this->$key = $value;
                        break;
                }
            } 
        }
    }
    
    /**
     * Validate the element prior to being saved.  
     * 
     * Checks the following criteria:
     * <ul>
     *  <li>Name is not empty.</li>
     *  <li>Has a data type.</li>
     *  <li>Has a record type.</li>
     *  <li>Name does not already exist within the given element set.</li>
     * </li>
     */
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Name must not be empty!');
        }
        
        if (empty($this->data_type_id)) {
            $this->addError('data_type_id', 'Element must have a valid data type!');
        }
        
        if (empty($this->record_type_id)) {
            $this->addError('record_type_id', 'Element must have a valid record type!');
        }
        
        // Check if the element set / element name combination already exists.
        if ($this->_nameIsInSet($this->name, $this->element_set_id)) {
            $this->addError('name', "'$this->name' already exists for element set #$this->element_set_id");
        }
    }
    
    /**
     * When deleting this element, delete all ElementText records associated 
     * with this element.
     */
    protected function _delete()
    {
        // Cascade delete all element texts associated with an element when deleting the element.
        $elementTexts = $this->getTable('ElementText')->findByElement($this->id);
        foreach ($elementTexts as $elementText) {
            $elementText->delete();
        }
    }
    
    /**
     * Set the default record & data type for the element (if necessary).
     */
    protected function beforeValidate()
    {
        if (empty($this->data_type_id)) {
            $this->data_type_id = $this->_getDataTypeId(self::DEFAULT_DATA_TYPE);
        }
        
        if (empty($this->record_type_id)) {
            $this->record_type_id = $this->_getRecordTypeId(self::DEFAULT_RECORD_TYPE);
        }
    }
        
    /**
     * Retrieve the record type ID from the name.
     */
    private function _getRecordTypeId($recordTypeName)
    {
        return $this->getDb()->getTable('RecordType')->findIdFromName($recordTypeName);
    }
    
    /**
     * Retrieve the data type ID from the name.
     */
    private function _getDataTypeId($dataTypeName)
    {
        return $this->getDb()->getTable('DataType')->findIdFromName($dataTypeName);
    }
    
    /**
     * Retrieve the element set ID from the name.
     */
    private function _getElementSetId($elementSetName)
    {
        $elementSet = $this->getDb()->getTable('ElementSet')->findBySql('name = ?', array($elementSetName), true);
        if (!$elementSet) {
            throw new Omeka_Record_Exception("Cannot set element set ID: set named '$elementSetName' does not exist.");
        }
        return $elementSet->id;
    }
    
    /**
     * Calculate whether the element's name already belongs to the current set.
     */
    private function _nameIsInSet($elementName, $elementSetId)
    {
        $db = $this->getDb();
        $sql = "SELECT COUNT(e.id) FROM $db->Element e WHERE e.name = ? AND e.element_set_id = ?";
        $params = array($elementName, $elementSetId);
        if ($this->exists()) {
            $sql .= " AND e.id != ?";
            $params[] = $this->id;
        }
        return (boolean)$db->fetchOne($sql, $params);
    }
}
