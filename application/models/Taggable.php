<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Taggable
 * Adaptation of the Rails Acts_as_taggable
 *
 * @package Omeka
 * @subpackage Mixins
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Taggable extends Omeka_Record_Mixin
{
    public $tagTable;
    public $joinTable;
    
    public function __construct(Omeka_Record $record) {
        
        $this->record = $record;
        
        $this->type = get_class($record);

        $this->tagTable = $this->getDb()->getTable('Tag');
        $this->joinTable = $this->getDb()->getTable('Taggings');
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
    
    public function deleteTaggings()
    {
        $db = $this->getDb();
        
        $db->delete($db->Taggings, array(
            'relation_id = ?' => (int) $this->record->id,
            'type = ?' => $this->type
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
        return $this->joinTable->findBy(array('record'=>$this->record));
    }
    
    /**
     * Get all the Tag records associated with this record
     * @param $order The ordering of the tags. By default, it sorts the tags alphabetically.
     * @see TagTable::applySearchFilters
     * @return array of Tag
     */
    public function getTags($order=array('alpha'))
    {
        return $this->tagTable->findBy(array('record'=>$this->record, 'sort'=>$order));
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
    public function deleteTags($tags, $delimiter=null)
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
        $findWith['record'] = $this->record;
        
        $taggings = $this->joinTable->findBy($findWith);
        foreach ($taggings as $tagging) {
            $tagging->delete();
        }
        
        return (!empty($taggings));
    }
            
    /**
     * If the $tag were a string and the keys of Tags were just the names of the tags, this would be:
     * in_array(array_keys($this->Tags))
     *
     * @return boolean
     */
    public function hasTag($tag) {
        $count = $this->joinTable->count(array('tag'=>$tag, 'record'=>$this->record));  
        return $count > 0;
    }    
    
    /**
     * Converts a delimited string of tags into an array of tag strings
     *
     * @param string $string A delimited string of tags
     * @return array An array of tag strings
     */
    protected function _getTagsFromString($string, $delimiter=null)
    {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        return array_diff(array_map('trim', explode($delimiter, $string)), array(''));
    }
    
    /**
     * Add tags for the record
     *
     * @param array|string $tags Either an array of tags or a delimited string
     * @return void
     */    
    public function addTags($tags, $delimiter=null) {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        
        if (!$this->record->id) {
            throw new Omeka_Record_Exception( __('A valid record ID # must be provided when tagging.') );
        }
        
        if (!is_array($tags)) {
            $tags = $this->_getTagsFromString($tags, $delimiter);
        }
        
        foreach ($tags as $key => $tagName) {
            $tag = $this->tagTable->findOrNew(trim($tagName));
            
            if (!$tag->exists()) {
                $tag->forceSave();
            }
            
            $join = new Taggings;
                        
            $join->tag_id = $tag->id;
            $join->relation_id = $this->record->id;
            $join->type = $this->type;
            $join->save();            
        }
    }

    /**
     * Calculate the difference between a tag string and a set of tags
     * @return array Keys('removed','added')
     */
    public function diffTagString($string, $tags=null, $delimiter=null)
    {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        
        if (!$tags) {
            $tags = $this->record->Tags;
        }
        
        $inputTags = $this->_getTagsFromString($string, $delimiter);
        
        $existingTags = array();
        
        foreach ($tags as $key => $tag) {
            if ($tag instanceof Tag || is_array($tag)) {
                $existingTags[$key] = trim($tag["name"]);
            } else {
                $existingTags[$key] = trim($tag);
            }   
        }
        
        if (!empty($existingTags)) {
            $removed = array_values(array_diff($existingTags,$inputTags));
        }
        
        if (!empty($inputTags)) {
            $added = array_values(array_diff($inputTags,$existingTags));
        }
        return compact('removed','added');
    }    
    
    /**
     * This will add tags that are in the tag string and remove those that are no longer in the tag string
     *
     * @param string $string A string of tags delimited by $delimiter
     * @return void
     */
    public function applyTagString($string, $delimiter=null)
    {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }

        $tags = $this->record->Tags;
        $diff = $this->diffTagString($string, $tags, $delimiter);
                
        if (!empty($diff['added'])) {
            $this->addTags($diff['added']);
            //PLUGIN HOOKS
            fire_plugin_hook('add_' . strtolower(get_class($this->record)) . '_tag',  $this->record, $diff['added']);
        }

        if (!empty($diff['removed'])) {
            $this->deleteTags($diff['removed']);
            //PLUGIN HOOKS
            fire_plugin_hook('remove_' . strtolower(get_class($this->record)) . '_tag',  $this->record, $diff['removed']);
        } 
    }
}
