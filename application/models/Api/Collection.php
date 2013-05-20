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

class Api_Collection extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST API representation for Collection
     * 
     * @param Collection $record 
     * @return array 
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record) {
        $representation = array(
            'id' => $record->id, 
            'url' => $this->getResourceUrl("/collections/{$record->id}"), 
            'public' => (bool) $record->public, 
            'featured' => (bool) $record->featured, 
            'added' => $this->getDate($record->added), 
            'modified' => $this->getDate($record->modified), 
            'owner' => array(
                'id'  => $record->owner_id,
                'url' => $this->getResourceUrl("/users/{$record->owner_id}"),
            ), 
            'items' => array(
                'count' => $record->getTable('Item')->count(array('collection_id' => $record->id)),
                'url' => $this->getResourceUrl("/items?collection={$record->id}"), 
            ), 
            'element_texts' => $this->getElementTextRepresentations($record), 
        );
        
        return $representation;
    }
    
    /**
     * Set data to a Collection.
     * 
     * @param Collection $record
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->public)) {
            $record->public = $data->public;
        }
        if (isset($data->featured)) {
            $record->featured = $data->featured;
        }
        $this->setElementTextData($record, $data);
    }
}
