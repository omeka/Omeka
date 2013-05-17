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
            'url' => $this->getResourceUrl("/items/{$record->id}"), 
            'public' => (bool) $record->public, 
            'featured' => (bool) $record->featured, 
            'added' => $this->getDate($record->added), 
            'modified' => $this->getDate($record->modified), 
        );
        if ($record->item_type_id) {
            $representation['item_type'] = array(
                'id' => $record->item_type_id, 
                'url' => $this->getResourceUrl("/item_types/{$record->item_type_id}"), 
                'name' => $record->Type->name, 
            );
        } else {
            $representation['item_type'] = null;
        }
        if ($record->collection_id) {
            $representation['collection'] = array(
                'id' => $record->collection_id, 
                'url' => $this->getResourceUrl("/collections/{$record->collection_id}"), 
            );
        } else {
            $representation['collection'] = null;
        }
        if ($record->owner_id) {
            $representation['owner'] = array(
                'id' => $record->owner_id, 
                'url' => $this->getResourceUrl("/users/{$record->owner_id}"), 
            );
        } else {
            $representation['owner'] = null;
        }
        $representation['files'] = array(
            'count' => $record->getTable('File')
                ->count(array('item_id' => $record->id)), 
            'url' => $this->getResourceUrl("/files?item={$record->id}"), 
        );
        $representation['element_texts'] = $this->getElementTextRepresentations($record);
        return $representation;
    }
    
    /**
     * Set data to an item.
     * 
     * @param Item $record
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
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
        $this->setElementTextData($record, $data);
    }
}
