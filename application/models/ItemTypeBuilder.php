<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Build an Item Type.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class ItemTypeBuilder extends Omeka_Record_Builder
{
    protected $_recordClass = 'ItemType';
    
    protected $_settableProperties = array('name', 'description');
    
    private $_elements = array();
    
    public function __construct($metadata = array(), $elements = array(), $record = null)
    {
        $this->_elements = $elements;
        parent::__construct($metadata, $record);
    }
    
    /**
     * Add elements to be associated with the Item Type.
     */
    protected function _beforeBuild()
    {        
        $this->_record->addElements($this->_elements);
    }
}
