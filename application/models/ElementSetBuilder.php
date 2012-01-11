<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Build an element set.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class ElementSetBuilder extends Omeka_Record_Builder
{
    protected $_settableProperties = array('name', 'description', 'record_type_id');
    protected $_recordClass = 'ElementSet';
    
    private $_elementInfo = array();
        
    /**
     * Set the elements to add to the element set.
     * 
     * @param array $elements
     */
    public function setElements(array $elements)
    {
        $this->_elementInfo = $elements;
    }
    
    /**
     * Overrides setRecordMetadata() to allow giving the name of the element as
     * a string.
     * 
     * @param string|array $metadata
     */
    public function setRecordMetadata($metadata)
    {
        // Set the element set name if a string is provided.
        if (is_string($metadata)) {
            $metadata = array('name' => $metadata);
        }
        
        // Determine the record type ID from the name.
        if (array_key_exists('record_type', $metadata)) {
            $recordType = $this->_db->getTable('RecordType')
                                    ->findBySql('name = ?', 
                                                array($metadata['record_type']),
                                                true);
            if (!$recordType) {
                throw new Omeka_Record_Builder_Exception( "Invalid record type named {$metadata[record_type]} provided!");
            }
            $metadata['record_type_id'] = $recordType->id;
        }
        
        return parent::setRecordMetadata($metadata);
    }
    
    /**
     * Add elements to be associated with the element set.
     */
    protected function _beforeBuild()
    {        
        // Add elements to the element set.
        $this->_record->addElements($this->_elementInfo);
    }    
}
