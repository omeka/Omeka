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
    
    private $_elementInfo = array();
    
    /**
     * Constructor.
     * 
     * @param array|string $metadata Metadata for the element set, or string
     * name of the element set.
     * @param array $elements
     * @param Omeka_Record|null
     **/
    public function __construct($metadata = array(), $elements = array(), $record = null)
    {
        if (is_string($metadata)) {
            $metadata = array('name'=>$metadata);
        }
        $this->_elementInfo = $elements;
        parent::__construct($metadata, $record);
    }
    
    /**
     * Add elements to be associated with the element set.
     */
    protected function _beforeBuild()
    {        
        // Add elements to the element set.
        $this->_record->addElements($this->_elementInfo);
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
