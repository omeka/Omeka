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
class Api_Element extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST API representation for an element.
     *
     * @param Element $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array(
            'id' => $record->id, 
            'url' => self::getResourceUrl("/elements/{$record->id}"), 
            'order' => $record->order, 
            'name' => $record->name, 
            'description' => $record->description, 
            'comment' => $record->comment, 
            'element_set' => array(
                'id' => $record->element_set_id, 
                'url'=> self::getResourceUrl("/element_sets/{$record->element_set_id}"), 
                'resource' => 'element_sets', 
            ), 
        );
        return $representation;
    }
    
    /**
     * Set POST data to an Element.
     *
     * @param Element $data
     * @param array $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->element_set->id)) {
            $record->element_set_id = $data->element_set->id;
        }
        if (isset($data->order)) {
            $record->order = $data->order;
        }
        if (isset($data->name)) {
            $record->name = $data->name;
        }
        if (isset($data->description)) {
            $record->description = $data->description;
        }
        if (isset($data->comment)) {
            $record->comment = $data->comment;
        }
    }
    
    /**
     * Set PUT data to an Element.
     *
     * @param Element $data
     * @param array $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->order)) {
            $record->order = $data->order;
        }
        if (isset($data->comment)) {
            $record->comment = $data->comment;
        }
    }
}
