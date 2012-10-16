<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Build an item type.
 * 
 * @package Omeka\Record\Builder
 */
class Builder_ItemType extends Omeka_Record_Builder_AbstractBuilder
{
    protected $_recordClass = 'ItemType';
    
    protected $_settableProperties = array('name', 'description');
    
    private $_elements = array();
        
    /**
     * Set the elements that will be attached to the built ItemType record.
     * 
     * @param array $elementMetadata
     * @return void
     */
    public function setElements(array $elementMetadata)
    {
        $this->_elements = $elementMetadata;
    }
    
    /**
     * Add elements to be associated with the Item Type.
     */
    protected function _beforeBuild()
    {        
        $this->_record->addElements($this->_elements);
    }
}
