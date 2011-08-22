<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Build an Item Type.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class ItemTypeBuilder extends Omeka_Record_Builder
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
