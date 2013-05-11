<?php

class API_ElementSet extends Omeka_Record_Api_AbstractRecordAdapter
{
    
    
    /**
     * Get the REST API representation for an element set.
     *
     * @param ElementSet $record
     * @return array
     */
    
    public function getRepresentation(Omeka_Record_AbstractRecord $record)    
    {
        $representation = array();
        $representation['id'] = $record->id;
        $representation['url'] = "/element_sets/{$record->id}";
        $representation['name'] = $record->name;
        $representation['description'] = $record->description;
        $representation['record_type'] = $record->record_type;
        $representation['elements'] = array(
                'count' => $record->getTable('Element')->count(array('element_set'=> $record->id)),
                'url' => "/elements?element_set={$record->id}"
                );
        return $representation;
    }
    
    
    
    /**
     * Set data to an ElementSet.
     *
     * @param ElementSet $data
     * @param array $data
     */
    public function setData(Omeka_Record_AbstractRecord $record, array $data)
    {
    
    }    
    
    
}