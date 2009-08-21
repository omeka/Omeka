<?php
class ItemOmekaXml extends Omeka_Output_Xml
{
    protected function _buildDoc()
    {
        // item
        $itemElement = $this->_createRootElement('item');
        
        // fileContainer
        $this->_buildFileContainerForItem($this->_record, $itemElement);
        
        // collection
        $this->_buildCollectionForItem($this->_record, $itemElement);
        
        // itemType
        $this->_buildItemTypeForItem($this->_record, $itemElement);
        
        // elementSetContainer
        $this->_buildElementSetContainerForRecord($this->_record, $itemElement);
        
        // tagContainer
        $this->_buildTagContainerForItem($this->_record, $itemElement);
        
        $this->_doc->appendChild($itemElement);
    }
}