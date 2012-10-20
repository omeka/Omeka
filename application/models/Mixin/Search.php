<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Make an Omeka record fulltext searchable.
 * 
 * Any class that extends Omeka_Record_AbstractRecord can be made searchable by 
 * pushing an instance of this mixin into Omeka_Record::$_mixins during 
 * Omeka_Record::_initializeMixins(). It must be pushed after all mixins that 
 * can add search text--for example, after ElementText.
 * 
 * The record type must also be registered using the search_record_types filter 
 * in order for the records to be searchable.
 * 
 * This mixin leverages the Omeka_Record_AbstractRecord::afterSave() and 
 * Omeka_Record_Mixin_AbstractMixin::afterSave() callbacks, so note their order 
 * of execution. Records that initialize ActsAsElementText will automatically 
 * add their element texts to the search text.
 * 
 * @see get_search_record_types()
 * @package Omeka\Record\Mixin
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
        $this->_text .= "$text ";
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
    public function afterSave($args)
    {
        self::saveSearchText(get_class($this->_record), $this->_record->id, 
            $this->_text, $this->_title, $this->_public);
    }
    
    /**
     * Delete this record's search text after it has been deleted.
     */
    public function afterDelete()
    {
        $searchText = $this->_record->getDb()->getTable('SearchText')
            ->findByRecord(get_class($this->_record), $this->_record->id);
        if ($searchText) {
            $searchText->delete();
        }
    }
    
    /**
     * Save a search text row.
     * 
     * Call this statically only when necessary. Used primarily when in a record 
     * that does not implement Mixin_Search but contains text that is needed for 
     * another record's search text. For example, when saving a child record 
     * that contains search text that should be saved to its parent record.
     * 
     * @param string $recordType
     * @param int $recordId
     * @param string $text
     * @param string $title
     * @param int $public
     */
    public static function saveSearchText($recordType, $recordId, $text, $title, $public = 1) {
        
        // Index this record only if it's of a type that is registered in the 
        // search_record_types filter.
        if (!array_key_exists($recordType, get_search_record_types())) {
            return;
        }
        
        $searchText = Zend_Registry::get('bootstrap')->getResource('Db')
            ->getTable('SearchText')->findByRecord($recordType, $recordId);
        
        // Either don't save the search text or delete an existing search text 
        // row if the record has no assigned text.
        if (!trim($text)) {
            if ($searchText) {
                $searchText->delete();
            }
            return;
        }
        
        if (!$searchText) {
            $searchText = new SearchText;
            $searchText->record_type = $recordType;
            $searchText->record_id = $recordId;
        }
        $searchText->public = $public ? 1 : 0;
        $searchText->title = $title;
        $searchText->text = $text;
        $searchText->save();
    }
}
