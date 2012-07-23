<?php
/**
 * Make an Omeka record fulltext searchable.
 * 
 * Any class that extends Omeka_Record can be made searchable by pushing an 
 * instance of this mixin into Omeka_Record::$_mixins during 
 * Omeka_Record::_initializeMixins(). It must be pushed after 
 * all mixins that can add search text--for example, after ActsAsElementText.
 * 
 * This mixin leverages the Omeka_Record::afterSave() and 
 * Omeka_Record_Mixin::afterSave() callbacks, so note their order of execution. 
 * Records that initialize ActsAsElementText will automatically add their 
 * element texts to the search text.
 */
class Mixin_Search extends Omeka_Record_Mixin
{
    protected $_text;
    protected $_title;
    protected $_public = true;
    
    public function __construct($record)
    {
        $this->_record = $record;
    }
    
    /**
     * Add search text to this record.
     * 
     * This method is meant to be called during afterSave().
     * 
     * @param string $text
     */
    public function addSearchText($text)
    {
        $this->_text .= " $text";
    }
    
    /**
     * Add a title to this record.
     * 
     * This method is meant to be called during afterSave().
     * 
     * @param string $title
     */
    public function setSearchTextTitle($title)
    {
        $this->_title = $title;
    }
    
    /**
     * Mark this record's search text as not public.
     * 
     * This method is meant to be called during afterSave().
     */
    public function setSearchTextPrivate()
    {
        $this->_public = false;
    }
    
    /**
     * Save the accumulated search text to the database.
     */
    public function afterSave()
    {
        $recordName = get_class($this->_record);
        $searchText = $this->_record->getDb()->getTable('SearchText')->findByRecord($recordName, $this->_record->id);
        if (!$searchText) {
            $searchText = new SearchText;
            $searchText->record_name = $recordName;
            $searchText->record_id = $this->_record->id;
        }
        $searchText->public = $this->_public;
        $searchText->title = $this->_title;
        $searchText->text = $this->_text;
        $searchText->save();
    }
    
    /**
     * Delete this record's search text after it has been deleted.
     */
    public function afterDelete()
    {
        $searchText = $this->_record->getDb()->getTable('SearchText')->findByRecord(get_class($this->_record), $this->_record->id);
        if ($searchText) {
            $searchText->delete();
        }
    }
}
