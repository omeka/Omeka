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
     * @return void
     */
    public function setRecordType($recordTypeName)
    {
        $this->record_type_id = $this->_getRecordTypeId($recordTypeName);
    }

    /**
     * Set the data type for the element (Text, Tiny Text, etc.).
     * @param DataType|string $dataType
     * @return void
     */
    public function setDataType($dataType)
    {
        if ($dataType instanceof DataType) {
            $this->data_type_id = $dataType->id;
        } else if (is_string($dataType)) {
            $dataTypeName = $dataType;
            $this->data_type_id = $this->_getDataTypeId($dataTypeName);
        }
    }

    /**
     * Get the data type object of the element
     * @return DataType
     */
    public function getDataType()
    {
        return $this->getDb()->getTable('DataType')->find($this->data_type_id);
    }

    /**
     * Set the element set for the element.
     * @param string $elementSetName
     * @return void
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
     * @return void
     */
    public function setOrder($order)
    {
        $this->order = (int)$order;
    }

    /**
     * Set the name of the element.
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = trim($name);
    }

    /**
     * Set the description for the element.
     * @param string $description
     * @return void
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
     * @return void
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
     * </ul>
     * @return void
     */
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', __('Name must not be empty!'));
        }

        if (empty($this->data_type_id)) {
            $this->addError('data_type_id', __('Element must have a valid data type!'));
        }

        if (empty($this->record_type_id)) {
            $this->addError('record_type_id', __('Element must have a valid record type!'));
        }

        // Check if the element set / element name combination already exists.
        if ($this->_nameIsInSet($this->name, $this->element_set_id)) {
            $this->addError('name', __('%1$s already exists for element set #%2$s', $this->name, $this->element_set_id) );
        }
    }

    /**
     * When deleting this element, delete all ElementText records associated
     * with this element.
     * @return void
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
     * @return void
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
     * @return RecordType
     */
    private function _getRecordTypeId($recordTypeName)
    {
        return $this->getDb()->getTable('RecordType')->findIdFromName($recordTypeName);
    }

    /**
     * Retrieve the data type ID from the name.
     * @return int
     */
    private function _getDataTypeId($dataTypeName)
    {
        return $this->getDb()->getTable('DataType')->findIdFromName($dataTypeName);
    }

    /**
     * Retrieve the element set ID from the name.
     * @return int
     */
    private function _getElementSetId($elementSetName)
    {
        $elementSet = $this->getDb()->getTable('ElementSet')->findBySql('name = ?', array($elementSetName), true);
        if (!$elementSet) {
            throw new Omeka_Record_Exception(__('Cannot set element set ID: set named "%s" does not exist.', $elementSetName));
        }
        return $elementSet->id;
    }

    /**
     * Calculate whether the element's name already belongs to the current set.
     * @return boolean
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
