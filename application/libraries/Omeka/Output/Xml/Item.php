<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Generates the omeka-xml output for Item records.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Output_Xml_Item extends Omeka_Output_Xml_Abstract
{
    /**
     * Create a node representing an Item record.
     *
     * @return void
     */
    protected function _buildNode()
    {
        // item
        $itemElement = $this->_createElement('item', null, $this->_record->id);
        
        $itemElement->setAttribute('public', $this->_record->public);
        $itemElement->setAttribute('featured', $this->_record->featured);
        
        if (!in_array($this->_context, array('file'))) {
            // fileContainer
            $this->_buildFileContainerForItem($this->_record, $itemElement);
        }
        
        if (!in_array($this->_context, array('collection'))) {
            // collection
            $this->_buildCollectionForItem($this->_record, $itemElement);
        }
        
        // itemType
        $this->_buildItemTypeForItem($this->_record, $itemElement);
        
        // elementSetContainer
        $this->_buildElementSetContainerForRecord($this->_record, $itemElement);
        
        // tagContainer
        $this->_buildTagContainerForItem($this->_record, $itemElement);
        
        $this->_node = $itemElement;
    }
}
