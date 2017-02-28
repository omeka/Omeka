<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Generates the omeka-xml output for Item records.
 * 
 * @package Omeka\Output
 */
class Output_ItemOmekaXml extends Omeka_Output_OmekaXml_AbstractOmekaXml
{
    /**
     * Create a node representing an Item record.
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
