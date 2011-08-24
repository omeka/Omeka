<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */
 
/**
 * Generates the container element for items in the omeka-xml output format.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Output_Xml_ItemContainer extends Omeka_Output_Xml_Abstract
{
    /**
     * Create a node to contain Item nodes.
     *
     * @see Omeka_Output_Xml_Item
     * @return void
     */
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
