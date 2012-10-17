<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Generates the container element for items in the omeka-xml output format.
 * 
 * @package Omeka\Output
 */
class Output_ItemContainerOmekaXml extends Omeka_Output_OmekaXml_AbstractOmekaXml
{
    /**
     * Create a node to contain Item nodes.
     *
     * @see Output_ItemOmekaXml
     * @return void
     */
    protected function _buildNode()
    {
        $itemContainerElement = $this->_createElement('itemContainer');
        
        $this->_setContainerPagination($itemContainerElement);
        
        foreach ($this->_record as $item) {
            $itemOmekaXml = new Output_ItemOmekaXml($item, $this->_context);
            $itemElement = $this->_doc->importNode($itemOmekaXml->_node, true);
            $itemContainerElement->appendChild($itemElement);
        }
        $this->_node = $itemContainerElement;
    }
}
