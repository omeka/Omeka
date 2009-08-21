<?php
class ItemOmekaXml extends Omeka_Output_Xml
{
    protected function _buildNode()
    {
        // item
        $itemElement = $this->_createElement('item', null, $this->_record->id);
        
        if ('item' == $this->_context) {
            // fileContainer
            $this->_buildFileContainerForItem($this->_record, $itemElement);
        }
        
        // collection
        $this->_buildCollectionForItem($this->_record, $itemElement);
        
        // itemType
        $this->_buildItemTypeForItem($this->_record, $itemElement);
        
        // elementSetContainer
        $this->_buildElementSetContainerForRecord($this->_record, $itemElement);
        
        // tagContainer
        $this->_buildTagContainerForItem($this->_record, $itemElement);
        
        $this->_node = $itemElement;
    }
}