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

class Api_ItemType extends Omeka_Record_Api_AbstractRecordAdapter
{
    
    /**
     * Get the REST API representation for a file.
     *
     * @param File $record
     * @return array
     */
    
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array();
        $representation['id'] = $record->id;
        $representation['url'] = "/item_types/{$record->id}";
        $representation['name'] = $record->name;
        $representation['description'] = $record->description;
        $representation['elements'] = array(
                'count' => $record->getTable('Element')->count(array('only_item_type_id'=>$record->id)),
                'url' => "elements/?item_type={$record->id}"
                );
        
        return $representation;
    } 
    
    /**
     * Set data to a record.
     *
     * @param Omeka_Record_AbstractRecord $data
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
    {
        
    }    
}
