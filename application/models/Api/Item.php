<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Record\Api
 */
class Api_Item extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST representation of an item.
     * 
     * @param Item $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array(
            'id' => $record->id, 
            'url' => self::getResourceUrl("/items/{$record->id}"), 
            'public' => (bool) $record->public, 
            'featured' => (bool) $record->featured, 
            'added' => self::getDate($record->added), 
            'modified' => self::getDate($record->modified), 
        );
        if ($record->item_type_id) {
            $representation['item_type'] = array(
                'id' => $record->item_type_id, 
                'url' => self::getResourceUrl("/item_types/{$record->item_type_id}"), 
                'name' => $record->Type->name, 
                'resource' => 'item_types', 
            );
        } else {
            $representation['item_type'] = null;
        }
        if ($record->collection_id) {
            $representation['collection'] = array(
                'id' => $record->collection_id, 
                'url' => self::getResourceUrl("/collections/{$record->collection_id}"), 
                'resource' => 'collections', 
            );
        } else {
            $representation['collection'] = null;
        }
        if ($record->owner_id) {
            $representation['owner'] = array(
                'id' => $record->owner_id, 
                'url' => self::getResourceUrl("/users/{$record->owner_id}"), 
                'resource' => 'users', 
            );
        } else {
            $representation['owner'] = null;
        }
        $representation['files'] = array(
            'count' => $record->getTable('File')
                ->count(array('item_id' => $record->id)), 
            'url' => self::getResourceUrl("/files?item={$record->id}"), 
            'resource' => 'files', 
        );
        $representation['tags'] = $this->getTagRepresentations($record);
        $representation['element_texts'] = $this->getElementTextRepresentations($record);
        return $representation;
    }
    
    /**
     * Set POST data to an item.
     * 
     * @param Item $record
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->item_type->id)) {
            $record->item_type_id = $data->item_type->id;
        }
        if (isset($data->collection->id)) {
            $record->collection_id = $data->collection->id;
        }
        if (isset($data->public)) {
            $record->public = $data->public;
        }
        if (isset($data->featured)) {
            $record->featured = $data->featured;
        }
        $this->setTagData($record, $data);
        $this->setElementTextData($record, $data);
    }
    
    /**
     * Set PUT data to an item.
     * 
     * @param Item $record
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        $this->setPostData($record, $data);
    }
}
