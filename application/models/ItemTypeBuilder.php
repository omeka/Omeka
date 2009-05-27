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
    
    /**
     * Add elements to be associated with the Item Type.
     */
    protected function _afterBuild()
    {
        $elementInfos = $this->_metadataOptions['_element_info'];
        
        foreach($elementInfos as $elementName => $elementConfig) {        
            $elementDescription = $elementConfig['description'];
            $elementDataTypeName = $elementConfig['data_type_name'];
            $this->_record->addElementByName($elementName, $elementDescription, $elementDataTypeName);   
        }
    }
}
