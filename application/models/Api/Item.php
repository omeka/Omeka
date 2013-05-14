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
        // Convert dates to UTC.
        $added = new DateTime($record->added);
        $modified = new DateTime($record->modified);
        $timezone = new DateTimeZone('UTC');
        
        $representation = array(
            'id' => $record->id, 
            'url' => "/items/{$record->id}", 
            'public' => (bool) $record->public, 
            'featured' => (bool) $record->featured, 
            'added' => $added->setTimezone($timezone)->format('c'), 
            'modified' => $modified->setTimezone($timezone)->format('c'), 
        );
        if ($record->item_type_id) {
            $representation['item_type'] = array(
                'id' => $record->item_type_id, 
                'url' => "/item_types/{$record->item_type_id}", 
                'name' => $record->Type->name, 
            );
        } else {
            $representation['item_type'] = null;
        }
        if ($record->collection_id) {
            $representation['collection'] = array(
                'id' => $record->collection_id, 
                'url' => "/collections/{$record->collection_id}", 
            );
        } else {
            $representation['collection'] = null;
        }
        if ($record->owner_id) {
            $representation['owner'] = array(
                'id' => $record->owner_id, 
                'url' => "/users/{$record->owner_id}", 
            );
        } else {
            $representation['owner'] = null;
        }
        $representation['files'] = array(
            'count' => $record->getTable('File')
                ->count(array('item_id' => $record->id)), 
            'url' => "/files?item={$record->id}", 
        );
        $representation['element_texts'] = $this->getElementTextRepresentations($record);
        return $representation;
    }
    
    /**
     * Set data to an item.
     * 
     * @param Item $data
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->item_type_id)) {
            $record->item_type_id = $data->item_type_id;
        }
        if (isset($data->collection_id)) {
            $record->collection_id = $data->collection_id;
        }
        if (isset($data->public)) {
            $record->public = $data->public;
        }
        if (isset($data->featured)) {
            $record->featured = $data->featured;
        }
    }
}
