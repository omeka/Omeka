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
    public function __construct(Omeka_Record $record) {
        
        $this->record = $record;
        
        $this->type = get_class($record);
        
        $this->conn = $this->getDb();
    }
    
    public function __get($prop)
    {
        switch ($prop) {
            case 'tagTable':
                return $this->getDb()->getTable('Tag');
                break;
            case 'joinTable':
                return $this->getDb()->getTable('Taggings');
            default:
                throw new Omeka_Record_Exception('No property exists!');
                break;
        }
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
        $id = (int) $this->record->id;
        
        $db = $this->getDb();
        
        //What table should we be deleting taggings for
        $record_type = $this->type;
        $model_table = $db->$record_type;

        //Delete everything from the taggings table
        
        $delete = "
        DELETE $db->Taggings 
        FROM $db->Taggings
        LEFT JOIN $model_table 
        ON $db->Taggings.relation_id = $model_table.id
        WHERE $model_table.id = $id 
        AND $db->Taggings.type = '$record_type'";
        
        $db->exec($delete);
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
        return $this->tagTable->findBy(array('record'=>$this->record, 'for'=>$this->type, 'sort'=>$order));
    }

    /**
     * Get all the Tag records associated with an entity or user
     * @param $entity The entity or user that owns the tags
     * @param $order The ordering of the tags.
     * @see TagTable::applySearchFilters
     * @return array of Tag
     */
    public function entityTags($entity, $order=array('alpha'))
    {
        return $this->tagTable->findBy(array('entity'=>$entity, 'record'=>$this->record, 'for'=>$this->type, 'sort'=>$order));
    }
    
    /**
     * Delete a tag from the record
     *
     * @param string|array $tags The tag name or array of tag names to delete from the record
     * @param Entity|User $entityOrUser The entity or user from which to specifically delete the tag
     * @param bool $deleteAll Whether or not to delete all references to this tag for this record
     * @param string $delimiter The delimiter of the tags. Not applicable if $tags is an array
     * @return bool Returns whether a tag in $tags was deleted. 
     *              Returns false if $tags is empty. 
     *              Returns true if at least one tag in $tags is deleted.
     */
    public function deleteTags($tags, $entityOrUser=null, $deleteAll=false, $delimiter=null)
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
        
        //If we aren't deleting all the tags associated with a record, then find those specifically for the user
        if (!$deleteAll) {
            if ($entityOrUser instanceof User) {
                $findWith['user'] = $entityOrUser;
            } else {
                $findWith['entity'] = $entityOrUser;
            }
        }
        
        $taggings = $this->joinTable->findBy($findWith);
        foreach ($taggings as $tagging) {
            $tagging->delete();
        }
        
        return (!empty($taggings));
    }
            
    /** If the $tag were a string and the keys of Tags were just the names of the tags, this would be:
     * in_array(array_keys($this->Tags))
     *
     * @return boolean
     */
    public function hasTag($tag, $entity=null) {
        $count = $this->joinTable->findBy(array('tag'=>$tag, 'entity'=>$entity, 'record'=>$this->record), null, true);  
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
     * Add tags for the record and for a specific entity
     *
     * @param array|string $tags Either an array of tags or a delimited string
     * @param Entity|User $entityOrUser The entity or user (in record form, for which a set of tags should be added)
     * @return void
     */    
    public function addTags($tags, $entityOrUser, $delimiter=null) {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        
        if (!$this->record->id) {
            throw new Omeka_Record_Exception( __('A valid record ID # must be provided when tagging.') );
        }
        
        if (!$entityOrUser) {
            throw new Omeka_Record_Exception( __('A valid entity or user must be provided when tagging.') );
        }
        
        if (!is_array($tags)) {
            $tags = $this->_getTagsFromString($tags, $delimiter);
        }
        
        if ($entityOrUser instanceof User) {
            $entityId = $entityOrUser->entity_id;
        } else {
            $entityId = $entityOrUser->id;
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
            $join->entity_id = $entityId;
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
     * @param Entity $entity The entity that all the tags will be associated with
     * @param bool $deleteTags When a tag is designated for removal, this specifies whether to remove all instances of the tag or just for the current Entity
     * @return void
     */
    public function applyTagString($string, $entity, $deleteTags = false, $delimiter=null)
    {
        // Set the tag_delimiter option if no delimiter was passed.
        if (is_null($delimiter)) {
            $delimiter = get_option('tag_delimiter');
        }
        
        // add and remove taggings by entity for the record
        $tags = $this->entityTags($entity);
        $diff = $this->diffTagString($string, $tags, $delimiter);
                
        if (!empty($diff['added'])) {
            $this->addTags($diff['added'], $entity);
            //PLUGIN HOOKS
            fire_plugin_hook('add_' . strtolower(get_class($this->record)) . '_tag',  $this->record, $diff['added'], $entity);
        }
        
        // if required, remove all instances of tag for the record, otherwise just remove the taggings from the entity
        if ($deleteTags) {
          $tags = $this->record->Tags;
          $diff = $this->diffTagString($string, $tags, $delimiter);
        }
        
        if (!empty($diff['removed'])) {
            $this->deleteTags($diff['removed'], $entity, $deleteTags);
            //PLUGIN HOOKS
            fire_plugin_hook('remove_' . strtolower(get_class($this->record)) . '_tag',  $this->record, $diff['removed'], $entity);
        } 
    }
}
