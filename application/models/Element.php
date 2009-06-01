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

    /**
     * Whether or not to attempt to order the element within its element set
     * when inserting the element into the database.
     */
    private $_autoOrder = false;

    const DEFAULT_RECORD_TYPE = 'Item';
    const DEFAULT_DATA_TYPE = 'Text';

    public function setRecordType($recordTypeName)
    {
        $this->record_type_id = $this->_getRecordTypeId($recordTypeName);
    }
    
    public function setDataType($dataTypeName)
    {
        $this->data_type_id = $this->_getDataTypeId($dataTypeName);
    }
    
    public function setElementSet($elementSetName)
    {
        $this->element_set_id = $this->_getElementSetId($elementSetName);
    }
    
    public function setOrder($order)
    {
        $this->order = (int)$order;
    }
    
    public function setAutoOrder($flag)
    {
        $this->_autoOrder = (boolean)$flag;
    }
    
    public function setName($name)
    {
        $this->name = trim($name);
    }
    
    public function setDescription($description)
    {
        $this->description = (string)trim($description);
    }
    
    /**
     * @param array|string $data If string, it's the name of the element.  
     * Otherwise, array of metadata for the element.
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
    
    protected function beforeInsert()
    {
        if ($this->_autoOrder) {
             throw new Omeka_Record_Exception('Implement auto-order!');
        }
    }
    
    private function _getRecordTypeId($recordTypeName)
    {
        return $this->getDb()->getTable('RecordType')->findIdFromName($recordTypeName);
    }
    
    private function _getDataTypeId($dataTypeName)
    {
        return $this->getDb()->getTable('DataType')->findIdFromName($dataTypeName);
    }
    
    private function _getElementSetId($elementSetName)
    {
        $elementSet = $this->getDb()->getTable('ElementSet')->findBySql('name = ?', array($elementSetName), true);
        return $elementSet->id;
    }
    
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
    
    private function _getNextElementOrder()
    {
        $db = $this->getDb();
        $sql = "
        SELECT MAX(`order`) + 1 
        FROM $db->Element e 
        WHERE e.`element_set_id` = ?";
        $nextElementOrder = $db->fetchOne($sql, $this->element_set_id);
        // In MySQL, NULL + 1 = NULL.
        if (!$nextElementOrder) {
            $nextElementOrder = 1;
        }
        return $nextElementOrder;
    }
}
