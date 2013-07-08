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
            'url' => self::getResourceUrl("/files/{$record->id}"),
            'file_urls' => array(
                'original' => $record->getWebPath(), 
                'fullsize' => $record->has_derivative_image ? $record->getWebPath('fullsize') : null, 
                'thumbnail' => $record->has_derivative_image ? $record->getWebPath('thumbnail') : null, 
                'square_thumbnail' => $record->has_derivative_image ? $record->getWebPath('square_thumbnail') : null, 
            ), 
            'added' => self::getDate($record->added), 
            'modified' => self::getDate($record->modified),
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
            'url'=> self::getResourceUrl("/items/{$record->item_id}"), 
            'resource' => 'items', 
        );
        $representation['element_texts'] = $this->getElementTextRepresentations($record);
        
        return $representation;
    }
    
    /**
     * Set POST data to a file.
     * 
     * @param File $record
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->order)) {
            $record->order = $data->order;
        }
        $this->setElementTextData($record, $data);
    }
    
    /**
     * Set PUT data to a file.
     * 
     * @param File $record
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        $this->setPostData($record, $data);
    }
}
