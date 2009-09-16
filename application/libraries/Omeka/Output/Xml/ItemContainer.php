<?php
class Omeka_Output_Xml_ItemContainer extends Omeka_Output_Xml_Abstract
{
    protected function _buildNode()
    {
        $itemContainerElement = $this->_createElement('itemContainer');
        
        $this->_setContainerPagination($itemContainerElement);
        
        foreach ($this->_record as $item) {
            $itemOmekaXml = new Omeka_Output_Xml_Item($item, $this->_context);
            $itemElement = $this->_doc->importNode($itemOmekaXml->_node, true);
            $itemContainerElement->appendChild($itemElement);
        }
        $this->_node = $itemContainerElement;
    }
}