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
class Api_ElementSet extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST API representation for an element set.
     *
     * @param ElementSet $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array(
            'id' => $record->id, 
            'url' => self::getResourceUrl("/element_sets/{$record->id}"), 
            'name' => $record->name, 
            'description' => $record->description, 
            'record_type' => $record->record_type, 
            'elements' => array(
                'count' => $record->getTable('Element')->count(array('element_set' => $record->id)),
                'url' => self::getResourceUrl("/elements?element_set={$record->id}"), 
                'resource' => 'elements', 
            ), 
        );
        return $representation;
    }
    
    /**
     * Set data to an ElementSet.
     *
     * @param ElementSet $data
     * @param array $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->name)) {
            $record->name = $data->name;
        }
        if (isset($data->description)) {
            $record->description = $data->description;
        }
    }
}
