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
     * Get the REST API representation for an item type.
     *
     * @param ItemType $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array();
        $representation['id'] = $record->id;
        $representation['url'] = $this->getResourceUrl("/item_types/{$record->id}");
        $representation['name'] = $record->name;
        $representation['description'] = $record->description;
        $representation['elements'] = array(
            'count' => $record->getTable('Element')->count(array('item_type'=>$record->id)),
            'url' => $this->getResourceUrl("/elements/?item_type={$record->id}"), 
        );
        return $representation;
    } 
    
    /**
     * Set data to an item type.
     *
     * @param ItemType $data
     * @param mixed $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, $data)
    {
        
    }
}
