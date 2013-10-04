<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Record\Mixin
 */
class Mixin_Tag extends Omeka_Record_Mixin_AbstractMixin
{
    private $_tagTable;
    private $_joinTable;
    private $_type;
    private $_tagsToSave = array();
    
    public function __construct(Omeka_Record_AbstractRecord $record) {
        parent::__construct($record);
        
        $this->_type = get_class($record);

        $db = $this->_record->getDb();
        $this->_tagTable = $db->getTable('Tag');
        $this->_joinTable = $db->getTable('RecordsTags');
    }
    
    /**
     * Fires whenever deleting a record that is taggable
     * This will actually delete all the references to a specific tag for a specific record
     *
     * @return void
     */    
    public function beforeDelete()
    {
        $this->deleteTaggings();
    }
    
    public function afterSave($args)
    {
        // Save tag/record relations.
        $addedTags = array();
        foreach ($this->_tagsToSave as $tag) {
            $join = $this->_joinTable->findForRecordAndTag($this->_record, $tag);
            if (!$join) {
                $join = new RecordsTags;
                $addedTags[] = $tag;
            }
            $join->tag_id = $tag->id;
            $join->record_id = $this->_record->id;
            $join->record_type = $this->_type;
            $join->save();
        }
        if ($addedTags) {
            $nameForHook = strtolower($this->_type);
            fire_plugin_hook("add_{$nameForHook}_tag", array('record' => $this->_record, 'added' => $addedTags));
        }
        
        // Add tags to this record's search text.
        foreach ($this->getTags() as $tag) {
            $this->_record->addSearchText($tag->name);
        }
    }
    
    public function deleteTaggings()
    {
        $db = $this->_record->getDb();
        
        $db->delete($db->RecordsTags, array(
            'record_id = ?' => (int) $this->_record->id,
            'record_type = ?' => $this->_type
            )
        );
    }
    
    /**
     * Retrieve all the Taggings objects that represent between a specific tag and the current record
     * Called by whatever record has enabled this module
     *
     * @return array of Taggings
     */
    public function getTaggings()
    {
        return $this->_joinTable->findBy(array('record' => $this->_record));
    }
    
    /**
     * Get all the Tag records associated with this record
     * @param $order The ordering of the tags. By default, it sorts the tags alphabetically.
     * @see TagTable::applySearchFilters
     * @return array of Tag
     */
    public function getTags($order = array())
    {
        if(isset($order['sort_field'])) {
            $sortField = $order['sort_field'];
            if(isset($order['sort_dir'])) {
                $sortDir = $order['sort_dir'];
            } else {
                $sortDir = 'a';
            }
        } else {
            $sortField = 'name';
            $sortDir = 'a';
        }
        return $this->_tagTable->findBy(array('record' => $this->_record, 'sort_field' => $sortField, 'sort_dir' => $sortDir));
    }
    
    /**
     * Delete a tag from the record
     *
     * @param string|array $tags The tag name or array of tag names to delete from the record
     * @param string $delimiter The delimiter of the tags. Not applicable if $tags is an array
     * @return bool Returns whether a tag in $tags was deleted. 
     *              Returns false if $tags is empty. 
     *              Returns true if at least one tag in $tags is deleted.
     */
    public function deleteTags($tags, $delimiter = null)
    {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        
        if (!is_array($tags)) {
            $tags = $this->_getTagsFromString($tags, $delimiter);
        }
        
        if (empty($tags)) {
            return false;
        }
        
        $findWith['tag'] = $tags;
        $findWith['record'] = $this->_record;
        
        $taggings = $this->_joinTable->findBy($findWith);
        $removed = array();
        foreach ($taggings as $tagging) {
            $removed[] = $this->_tagTable->find($tagging->tag_id);
            $tagging->delete();
        }
        
        $nameForHook = strtolower($this->_type);
        fire_plugin_hook("remove_{$nameForHook}_tag", array('record' => $this->_record, 'removed' => $removed));        
        return !empty($taggings);
    }
            
    /**
     * If the $tag were a string and the keys of Tags were just the names of the tags, this would be:
     * in_array(array_keys($this->Tags))
     *
     * @return boolean
     */
    public function hasTag($tag) {
        $count = $this->_joinTable->count(array('tag'=>$tag, 'record'=>$this->_record));  
        return $count > 0;
    }    
    
    /**
     * Converts a delimited string of tags into an array of tag strings
     *
     * @param string $string A delimited string of tags
     * @return array An array of tag strings
     */
    protected function _getTagsFromString($string, $delimiter = null)
    {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        return array_diff(array_map('trim', explode($delimiter, $string)), array(''));
    }
    
    /**
     * Set tags to be saved to the record.
     *
     * @param array|string $tags Either an array of tags or a delimited string
     * @return void
     */    
    public function addTags($tags, $delimiter = null) {
        
        // If no delimiter was passed, set the delimiter.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        
        // If string was passed, build an array of tags.
        if (!is_array($tags)) {
            $tags = $this->_getTagsFromString($tags, $delimiter);
        }
        
        foreach ($tags as $tagName) {
            $this->_tagsToSave[] = $this->_tagTable->findOrNew(trim($tagName));
        }
    }
    
    /**
     * Apply tags 
     * 
     * @param array $inputTags
     */
    public function applyTags(array $inputTags)
    {
        $diff = $this->diffTags($inputTags);
        if (!empty($diff['added'])) {
            $this->addTags($diff['added']);
        }
        if (!empty($diff['removed'])) {
            $this->deleteTags($diff['removed']);
        }
    }
    
    /**
     * Calculate the difference between a tag string and a set of tags
     * @return array Keys('removed','added')
     */
    public function diffTags($inputTags, $tags = null)
    {
        if (!$tags) {
            $tags = $this->_record->Tags;
        }
        $existingTags = array();
        foreach ($tags as $key => $tag) {
            if ($tag instanceof Tag || is_array($tag)) {
                $existingTags[$key] = trim($tag["name"]);
            } else {
                $existingTags[$key] = trim($tag);
            }
        }
        if (!empty($existingTags)) {
            $removed = array_values(array_diff($existingTags, $inputTags));
        }
        if (!empty($inputTags)) {
            $added = array_values(array_diff($inputTags, $existingTags));
        }
        return compact('removed','added');
    }
    
    /**
     * This will add tags that are in the tag string and remove those that are 
     * no longer in the tag string
     *
     * @param string $string A string of tags delimited by $delimiter
     * @param string|null $delimiter
     */
    public function applyTagString($string, $delimiter = null)
    {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        $inputTags = $this->_getTagsFromString($string, $delimiter);
        $this->applyTags($inputTags);
    }
}
