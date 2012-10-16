<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Generates the omeka-xml output for File records.
 * 
 * @package Omeka\Output
 */
class Output_FileOmekaXml extends Omeka_Output_OmekaXml_AbstractOmekaXml
{
    /**
     * Create a node repesenting a File record.
     *
     * @return void
     */
    protected function _buildNode()
    {
        $fileElement = $this->_createElement('file', null, $this->_record->id);
        if ($this->_record->order) {
            $fileElement->setAttribute('order', $this->_record->order);
        }
        $srcElement = $this->_createElement('src', $this->_record->getWebPath(), 
            null, $fileElement);
        $authenticationElement = $this->_createElement('authentication', 
            $this->_record->authentication, null, $fileElement);
        $this->_buildElementSetContainerForRecord($this->_record, $fileElement);
        
        if (in_array($this->_context, array('file'))) {
            $item = get_db()->getTable('Item')->find($this->_record->item_id);
            $itemOmekaXml = new Output_ItemOmekaXml($item, $this->_context);
            $itemElement = $this->_doc->importNode($itemOmekaXml->_node, true);
            $fileElement->appendChild($itemElement);
        }
        
        $this->_node = $fileElement;
    }
}
