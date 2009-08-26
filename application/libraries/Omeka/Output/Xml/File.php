<?php
class Omeka_Output_Xml_File extends Omeka_Output_Xml_Abstract
{
    protected function _buildNode()
    {
        $fileElement = $this->_createElement('file', null, $this->_record->id);
        $srcElement = $this->_createElement('src', WEB_ARCHIVE . "/{$this->_record->archive_filename}", null, $fileElement);
        $authenticationElement = $this->_createElement('authentication', $this->_record->authentication, null, $fileElement);
        $this->_buildElementSetContainerForRecord($this->_record, $fileElement);
        
        if (in_array($this->_context, array('file'))) {
            $item = get_db()->getTable('Item')->find($this->_record->item_id);
            $itemOmekaXml = new Omeka_Output_Xml_Item($item, $this->_context);
            $itemElement = $this->_doc->importNode($itemOmekaXml->_node, true);
            $fileElement->appendChild($itemElement);
        }
        
        $this->_node = $fileElement;
    }
}