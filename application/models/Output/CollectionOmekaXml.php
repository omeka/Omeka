<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Generates the omeka-xml output for a collection.
 * 
 * @package Omeka\Output
 */
class Output_CollectionOmekaXml extends Omeka_Output_OmekaXml_AbstractOmekaXml
{
    /**
     * Create a node representing a collection.
     *
     * @return void
     */
    protected function _buildNode()
    {
        // collection
        $collectionElement = $this->_createElement('collection', null, $this->_record->id);
        $this->_buildElementSetContainerForRecord($this->_record, $collectionElement);
        $this->_buildItemContainerForCollection($this->_record, $collectionElement);
        $this->_node = $collectionElement;
    }
}
