<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A metadata element within an element set or item type.
 * 
 * @package Omeka\Record
 */
class Element extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    /**
     * ID of the ElementSet this Element belongs to.
     * 
     * @var int
     */
    public $element_set_id;

    /**
     * This Element's order within the parent ElementSet.
     * 
     * @var int
     */
    public $order;

    /**
     * A human-readable name
     *
     * @var string
     */
    public $name = '';

    /**
     * A human-readable description
     *
     * @var string
     */
    public $description = '';

    /**
     * A user-generated comment
     *
     * @var string
     */
    public $comment = '';

    /**
     * Set the parent ElementSet by name.
     * 
     * @param string $elementSetName
     */
    public function setElementSet($elementSetName)
    {
        $this->element_set_id = $this->_getElementSetId($elementSetName);
    }

    /**
     * Return the parent ElementSet object for this element.
     *
     * @return ElementSet|null
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
     * 
     * @param int $order
     */
    public function setOrder($order)
    {
        if ($order !== null) {
            $order = (int) $order;
        }
        
        $this->order = $order;
    }

    /**
     * Set the Element's name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = trim($name);
    }

    /**
     * Set the Element's description.
     * 
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = (string)trim($description);
    }

    /**
     * Set the Element's comment.
     *
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = trim($comment);
    }

    /**
     * Set the data for the Element in bulk.
     * 
     * @param array|string $data If string, the name of the element.
     * Otherwise, array of metadata for the element.  The array may contain the
     * following keys:
     *
     * * name
     * * description
     * * comment
     * * order
     * * element_set_id
     * * element_set
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
     * 
     * * Name is not empty.
     * * Name does not already exist within the given element set.
     */
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', __('The element name must not be empty.'));
        }
        
        if (!$this->getDb()->getTable('ElementSet')->exists($this->element_set_id)) {
            $this->addError('element_set_id', __('Invalid element set.'));
        }
        
        // Check if the element set / element name combination already exists.
        if ($this->_nameIsInSet($this->name, $this->element_set_id)) {
            $elementSetName = $this->getElementSet()->name;
            $this->addError('name', __('An element named "%s" already exists for the "%s" element set.', $this->name, $elementSetName) );
        }
    }

    /**
     * Delete associated records when deleting the Element.
     * 
     * Cascade delete to all element texts and item type assignments associated
     * with the element.
     */
    protected function _delete()
    {
        $elementTexts = $this->getTable('ElementText')->findByElement($this->id);
        foreach ($elementTexts as $elementText) {
            $elementText->delete();
        }
        $itemTypesElements = $this->getTable('ItemTypesElements')->findByElement($this->id);
        foreach ($itemTypesElements as $itemTypesElement) {
            $itemTypesElement->delete();
        }
    }

    /**
     * Get an element set ID from a name.
     * 
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
     * 
     * @return boolean
     */
    private function _nameIsInSet($elementName, $elementSetId)
    {
        $db = $this->getDb();
        $sql = "SELECT COUNT(id) FROM $db->Element WHERE name = ? AND element_set_id = ?";
        $params = array($elementName, $elementSetId);
        if ($this->exists()) {
            $sql .= " AND id != ?";
            $params[] = $this->id;
        }
        return (bool) $db->fetchOne($sql, $params);
    }
    
    /**
     * Identify Element records as relating to the Elements ACL resource.
     *
     * Required by Zend_Acl_Resource_Interface.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Elements';
    }
}
