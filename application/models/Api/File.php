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
class Api_File extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST API representation for a file.
     * 
     * @param File $record
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
            'added' => $added->setTimezone($timezone)->format('c'), 
            'modified' => $modified->setTimezone($timezone)->format('c'),
            'filename' => $record->filename,
            'authentication' => $record->authentication,
            'has_derivative_image' => (bool) $record->has_derivative_image,
            'mime_type' => $record->mime_type,
            'order' => $record->order,
            'original_filename' => $record->original_filename,
            'size' => $record->size,
            'stored' => (bool) $record->stored,
            'type_os' => $record->type_os, 
        );
        
        // Metadata is stored as a JSON string, so make it an array to fit into 
        // the API response
        $metadata = json_decode($record->metadata, true);
        //use ArrayObject to make sure we always get a JSON object, even when empty
        $representation['metadata'] = new ArrayObject($metadata);
        $representation['url'] = "/files/{$record->id}";
        $representation['item'] = array(
            "id" => $record->item_id, 
            "url"=>"/items/{$record->item_id}", 
        );
        
        $representation['element_texts'] = array(
            'count' => $record->getTable('ElementText')
                ->count(array('record_type' => 'File', 'record_id' => $record->id)),
            'url' => "/element_texts?record_type=File&record_id={$record->id}",
        );
        return $representation;
    }
    
    /**
     * Set data to an file.
     * 
     * @param File $data
     * @param array $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, array $data)
    {
        
    }
}
