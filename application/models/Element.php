<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An element and its metadata.
 * 
 * @package Omeka\Record
 */
class Element extends Omeka_Record_AbstractRecord
{
    public $element_set_id;
    public $order;
    public $name = '';
    public $description = '';
    public $comment = '';

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
    
    public function setComment($comment)
    {
        $this->comment = trim($comment);
    }

    /**
     * @param array|string $data If string, it's the name of the element.
     * Otherwise, array of metadata for the element.  May contain the following
     * keys in the array:
     * <ul>
     *  <li>name</li>
     *  <li>description</li>
     *  <li>comment</li>
     *  <li>order</li>
     *  <li>element_set_id</li>
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
                    case 'comment':
                        $this->setComment($value);
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
