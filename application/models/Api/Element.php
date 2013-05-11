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
     * Get the REST API representation for a element.
     *
     * @param Element $record
     * @return array
     */
        
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {
        $representation = array();
        $representation['id'] = $record->id;
        $representation['url'] = "/elements/{$record->id}";
        $representation['element_set'] = array('id'=>$record->element_set_id, 'url'=>"/element_sets/{$record->element_set_id}");
        $representation['order'] = $record->order;
        $representation['name'] = $record->name;
        $representation['description'] = $record->description;
        $representation['comment'] = $record->comment;
        $representation['element_texts'] = array(
                'count' => $record->getTable('ElementText')->count(array('element_id'=>$record->id)),
                'url' => "/element_texts?element={$record->id}"
                );
        return $representation;
    }
    
    
    /**
     * Set data to an Element.
     *
     * @param Element $data
     * @param array $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, array $data)
    {
    
    }    
}