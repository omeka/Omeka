<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ActsAsElementText extends Omeka_Record_Mixin
{
    
    protected $_textsByNaturalOrder;
    protected $_textsByElementId;
    protected $_elementsByNaturalOrder;
    protected $_elementsByNameAndSet;
    protected $_elementsBySet;
    protected $_textsToSave = array();
    
    public function __construct($record)
    {
        $this->_record = $record;
    }
    
    protected function getRecordType()
    {
        return get_class($this->_record);
    }
    
    protected function getTextRecordTypeId()
    {
        var_dump('get record type ID!');exit;
    }
    
    /**
     * Load all the ElementText records for the given record (Item, File, etc.).  These will be indexed by [element_id].
     * 
     * Also load all the Element records and index those by their name and set name.
     * 
     * @return void
     **/
    public function loadElementsAndTexts()
    {
        $elementTextRecords = $this->getElementTextRecords();
        
        $this->_textsByNaturalOrder = $elementTextRecords;
        $this->_textsByElementId = $this->indexByElementId($elementTextRecords);
        
        $elements = $this->getElements();
        $this->_elementsByNaturalOrder = $elements;
        $this->_elementsByNameAndSet = $this->indexElementsByNameAndSet($elements);
        $this->_elementsBySet = $this->indexElementsBySet($elements);
    }
    
    /**
     * Retrieve ALL of the ElementText records for the given record.
     * 
     * @return array Set of ElementText records for this record.
     **/
    public function getElementTextRecords()
    {
        return $this->_record->getTable('ElementText')->findByRecord($this->_record);
    }
    
    /**
     * Retrieve ALL of the Element records for the given record.
     * 
     * @return array
     **/
    public function getElements()
    {
        return $this->_record->getTable('Element')->findByRecordType($this->getRecordType());
    }

    /**
     * undocumented function
     * 
     * @param string
     * @return void
     **/
    public function getTextsByElement($element)
    {
        // Load 'em if we need 'em.
        if (!$this->_textsByElementId) {
            $this->loadElementsAndTexts();
        }
        
        $texts = @$this->_textsByElementId[$element->id];
        return !empty($texts) ? $texts : array();
    }
    
    public function getElementsBySetName($elementSetName)
    {
        if (!$this->_elementsBySet) {
            $this->loadElementsAndTexts();
        }
        
        $elements = @$this->_elementsBySet[$elementSetName];
        return !empty($elements) ? $elements : array();
    }
    
    /**
     * @todo Duplicated in ElementTextTable.  Remove from there and put here instead.
     * 
     * @param array
     * @return array
     **/
    public function indexByElementId($textRecords)
    {
        $indexed = array();
        foreach ($textRecords as $textRecord) {
            $indexed[$textRecord->element_id][] = $textRecord;
        }
        
        return $indexed;
    }
    
    /**
     * Index a set of Elements based on their name.
     * 
     * @param array
     * @return array
     **/
    protected function indexElementsByNameAndSet(array $elementRecords)
    {
        $indexed = array();
        foreach($elementRecords as $record) {
            $indexed[$record->name][$record->set_name] = $record;
        }
        return $indexed;        
    }
    
    /**
     * @todo May need to apply ksort() to this to ensure that all sub-arrays are in the correct order.
     * 
     * @param array
     * @return array
     **/
    protected function indexElementsBySet(array $elementRecords)
    {
        $indexed = array();
        foreach($elementRecords as $record) {
            $indexed[$record->set_name][(int) $record->order] = $record;
        }
        return $indexed;
    }
    
    /**
     * Add a string of text for an element.
     * 
     * Creates a new ElementText record, populates it with the specified text value 
     * and assigns it to the element.
     * 
     * @param string
     * @return void
     **/
    public function addTextForElement($element, $elementText, $isHtml = false)
    {
        $textRecord = new ElementText;
        $textRecord->record_id = $this->_record->id;
        $textRecord->element_id = $element->id;
        $textRecord->record_type_id = $this->getRecordTypeId();
        $textRecord->text = $elementText;
        $textRecord->html = (int)$isHtml;
        
        $element->addText($textRecord);
        
        $this->_textsToSave[] = $textRecord;
    }
    
    /**
     * Save all ElementText records that were associated with a record.
     *
     * Typically called in the afterSave() hook for a record.
     * 
     * @return void
     **/
    public function saveElementTexts()
    {
        var_dump($this->_textsToSave);exit;
    }
    
    /**
     * @todo Not implemented yet. Also, current behavior in Item model is to
     * delete all ElementText records, but for files this can't work b/c there
     * are some ElementText fields that are auto-generated from getID3. There is
     * no need to auto-delete these because they will never be modified on the
     * form. One solution is to display it on the form anyway and make it so
     * that those fields can't be edited.  Another solution would be to override 
     * behavior of this method in the File model so that it specifically does not 
     * delete the ElementText records belonging to auto-generated ID3 data.
     * 
     * @param string
     * @return void
     **/
    public function deleteElementTexts()
    {
        
    }
}
