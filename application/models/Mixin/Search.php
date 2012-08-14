<?php
/**
 * Make an Omeka record fulltext searchable.
 * 
 * Any class that extends Omeka_Record_AbstractRecord can be made searchable by 
 * pushing an instance of this mixin into Omeka_Record::$_mixins during 
 * Omeka_Record::_initializeMixins(). It must be pushed after all mixins that 
 * can add search text--for example, after ElementText.
 * 
 * This mixin leverages the Omeka_Record_AbstractRecord::afterSave() and 
 * Omeka_Record_Mixin_AbstractMixin::afterSave() callbacks, so note their order 
 * of execution. Records that initialize ActsAsElementText will automatically 
 * add their element texts to the search text.
 */
class Mixin_Search extends Omeka_Record_Mixin_AbstractMixin
{
    protected $_text;
    protected $_title;
    protected $_public = 1;
    
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
        $recordType = get_class($this->_record);
        
        // Index this record only if it's of a type that is registered in the 
        // search_record_types filter.
        if (!in_array($recordType, self::getSearchRecordTypes())) {
            return;
        }
        
        $searchText = $this->_record->getDb()->getTable('SearchText')->findByRecord($recordType, $this->_record->id);
        if (!$searchText) {
            $searchText = new SearchText;
            $searchText->record_type = $recordType;
            $searchText->record_id = $this->_record->id;
        }
        $searchText->public = $this->_public ? 1 : 0;
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
    
    /**
     * Get the search record types.
     * 
     * Returns an array containing all record types (i.e. class names) in the 
     * application/models directory that should be indexed and searchable. These 
     * classes must extend Omeka_Record_AbstractRecord and implement this search 
     * mixin.
     * 
     * @return array
     */
    public static function getSearchRecordTypes()
    {
        // Apply the filters only once.
        static $searchRecordTypes = null;
        if (!$searchRecordTypes) {
            $coreSearchRecordTypes = array('Item', 'File', 'Collection');
            try {
                $searchRecordTypes = Zend_Registry::get('pluginbroker')
                    ->applyFilters('search_record_types', $coreSearchRecordTypes);
            } catch (Zend_Exception $e) {
                $searchRecordTypes = $coreSearchRecordTypes;
            }
        }
        return $searchRecordTypes;
    }
}
