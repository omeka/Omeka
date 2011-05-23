<?php 
/**
 * @version $Id$
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
    protected $_settableProperties = array('name', 'description');
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
        if (is_string($metadata)) {
            $metadata = array('name'=>$metadata);
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
