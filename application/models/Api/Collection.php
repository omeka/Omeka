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
        // Convert Dates to UTC.
        $added = new DateTime($record->added);
        $modified = new DateTime($record->modified);
        $timezone = new DateTimeZone('UTC');
        
        $representation = array();
        $representation['id'] = $record->id;
        $representation['url'] = "/collections/{$record->id}";
        $representation['owner'] = array(
            'id'  => $record->owner_id,
            'url' => "/users/{$record->owner_id}",
        );
        $representation['public'] = (bool) $record->public;
        $representation['featured'] = (bool) $record->featured;
        $representation['added'] = $added->setTimezone($timezone)->format('c');
        $representation['modified'] = $modified->setTimezone($timezone)->format('c');
        $representation['items'] = array(
            'count' => $record->getTable('Item')
                              ->count(array('item_id' => $record->id)),
            'url' => "/items?collection={$record->id}",
        );
        $representation['element_texts'] = array(
            'count' => $record->getTable('ElementText')
                              ->count(array(
                                  'record_type' => 'Collection',
                                  'record_id'   => $record->id,
                              )),
            'url'  => "/element_texts?record_type=Collection&record_id={$record->id}",
        );
        
        return $representation;
    }
    
    /**
     * Set data to an Collection.
     * 
     * @param Collection $data
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
    {
        
    }
}
