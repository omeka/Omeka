<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Build a collection.
 * 
 * @package Omeka\Record\Builder
 */
class Builder_Collection extends Omeka_Record_Builder_AbstractBuilder
{    
    const OWNER_ID = 'owner_id';
    const IS_PUBLIC = 'public';
    const IS_FEATURED = 'featured';
    
    const OVERWRITE_ELEMENT_TEXTS = 'overwriteElementTexts';
    
    protected $_recordClass = 'Collection';
    protected $_settableProperties = array(
        self::OWNER_ID, 
        self::IS_PUBLIC, 
        self::IS_FEATURED
    );    
    
    private $_elementTexts = array();
    
    /**
     * Set the element texts for the collection.
     * 
     * @param array $elementTexts
     */    
    public function setElementTexts(array $elementTexts)
    {
        $this->_elementTexts = $elementTexts;
    }
    
    /**
     * Add element texts to a record.
     */            
    private function _addElementTexts()
    {
        return $this->_record->addElementTextsByArray($this->_elementTexts);
    }
    
    /**
     * Replace all the element texts for existing element texts.
     */    
    private function _replaceElementTexts()
    {
        $collection = $this->_record;
        // If this option is set, it will loop through the $elementTexts provided,
        // find each one and manually update it (provided it exists).
        // The rest of the element texts will get added as per usual.
        foreach ($this->_elementTexts as $elementSetName => $textArray) {
            foreach ($textArray as $elementName => $elementTextSet) {
                $etRecordSet = $collection->getElementTexts($elementSetName, $elementName);
                foreach ($elementTextSet as $elementTextIndex => $textAttr) {
                    // If we have an existing ElementText record, use that
                    // instead of adding a new one.
                    if (array_key_exists($elementTextIndex, $etRecordSet)) {
                        $etRecord = $etRecordSet[$elementTextIndex];
                        $etRecord->text = $textAttr['text'];
                        $etRecord->html = $textAttr['html'];
                        $etRecord->save();
                    } else {
                        // Otherwise we should just append the new text to the 
                        // pre-existing ones.
                        $elementRecord = $collection->getElement($elementSetName, $elementName);
                        $collection->addTextForElement($elementRecord, $textAttr['text'], $textAttr['html']);
                    }
                }
            }
        }
    }
       
    /**
     * Add elements associated with the collection.
     *
     * @param Omeka_Record_AbstractRecord $record The collection record
     */
    protected function _beforeBuild(Omeka_Record_AbstractRecord $record)
    {
        $metadata = $this->getRecordMetadata();
        
        if ($this->_record->exists() and array_key_exists(self::OVERWRITE_ELEMENT_TEXTS, $metadata)) {
            $this->_replaceElementTexts();
        } else {
            $this->_addElementTexts();
        }
    }
}