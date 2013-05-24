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
        $representation = array(
            'id' => $record->id,
            'url' => $this->getResourceUrl("/files/{$record->id}"),
            'added' => $this->getDate($record->added), 
            'modified' => $this->getDate($record->modified),
            'filename' => $record->filename,
            'authentication' => $record->authentication,
            'has_derivative_image' => (bool) $record->has_derivative_image,
            'mime_type' => $record->mime_type,
            'order' => $record->order,
            'original_filename' => $record->original_filename,
            'size' => $record->size,
            'stored' => (bool) $record->stored,
            'type_os' => $record->type_os, 
            'metadata' => json_decode($record->metadata, true) ,
        );
        $representation['item'] = array(
            'id' => $record->item_id, 
            'url'=> $this->getResourceUrl("/items/{$record->item_id}"), 
        );
        $representation['element_texts'] = $this->getElementTextRepresentations($record);
        
        return $representation;
    }
    
    /**
     * Set data to a file.
     * 
     * @param File $record
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->order)) {
            $record->order = $data->order;
        }
        $this->setElementTextData($record, $data);
    }
}
