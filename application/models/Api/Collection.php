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
            'url' => self::getResourceUrl("/collections/{$record->id}"), 
            'public' => (bool) $record->public, 
            'featured' => (bool) $record->featured, 
            'added' => self::getDate($record->added), 
            'modified' => self::getDate($record->modified), 
            'owner' => array(
                'id'  => $record->owner_id,
                'url' => self::getResourceUrl("/users/{$record->owner_id}"), 
                'resource' => 'users', 
            ), 
            'items' => array(
                'count' => $record->getTable('Item')->count(array('collection_id' => $record->id)),
                'url' => self::getResourceUrl("/items?collection={$record->id}"), 
                'resource' => 'items', 
            ), 
            'element_texts' => $this->getElementTextRepresentations($record), 
        );
        
        return $representation;
    }
    
    /**
     * Set POST data to a Collection.
     * 
     * @param Collection $record
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->public)) {
            $record->public = $data->public;
        }
        if (isset($data->featured)) {
            $record->featured = $data->featured;
        }
        $this->setElementTextData($record, $data);
    }
    
    /**
     * Set PUT data to a Collection.
     * 
     * @param Collection $record
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        $this->setPostData($record, $data);
    }
}
