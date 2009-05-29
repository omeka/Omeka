<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Build an element set.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class ElementSetBuilder extends Omeka_Record_Builder
{
    protected $_settableProperties = array('name', 'description');
    protected $_recordClass = 'ElementSet';
    
    /**
     * Add elements to be associated with the element set.
     */
    protected function _beforeBuild()
    {
        $elements = $this->_metadataOptions['_elements'];
        
        // Add elements to the element set.
        $this->_record->addElements($elements);
    }
    
    /**
     * Ensure that the element set name has been provided, trim whitespace from
     * name and description fields.
     */
    protected function _parseMetadataOptions(array $metadata)
    {
        if (!isset($metadata['name'])) {
            throw new Omeka_Record_Exception('An element set name was not given.');
        }

        // Trim whitespace from all array elements.
        $metadata['name'] = trim($metadata['name']);
        $metadata['description'] = trim($metadata['description']);
        
        return $metadata;
    }
}
