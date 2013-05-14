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
abstract class Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST representation of a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     */
    abstract public function getRepresentation(Omeka_Record_AbstractRecord $record);
    
    /**
     * Set data to a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param mixed $data
     */
    abstract public function setData(Omeka_Record_AbstractRecord $record, $data);
    
    /**
     * Get representations of element texts belonging to a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @return array
     */
    protected function getElementTextRepresentations(Omeka_Record_AbstractRecord $record)
    {
        $representations = array();
        $elementsCache = array();
        $elementSetsCache = array();
        
        foreach ($record->getAllElementTexts() as $elementText) {
            
            $representation = array(
                'html' => (bool) $elementText->html, 
                'text' => $elementText->text, 
            );
            
            // Cache the element to avoid unnecessary database queries.
            if (isset($elementsCache[$elementText->element_id])) {
                $element = $elementsCache[$elementText->element_id];
            } else {
                $element = get_db()->getTable('Element')->find($elementText->element_id);
                $elementsCache[$element->id] = $element;
            }
            
            // Cache the element set to avoid unnecessary database queries.
            if (isset($elementSetsCache[$element->element_set_id])) {
                $elementSet = $elementSetsCache[$element->element_set_id];
            } else {
                $elementSet = get_db()->getTable('ElementSet')->find($element->element_set_id);
                $elementSetsCache[$elementSet->id] = $elementSet;
            }
            
            $representation['element_set'] = array(
                'id' => $elementSet->id, 
                'url' => "/element_sets/{$elementSet->id}", 
                'name' => $elementSet->name, 
            );
            $representation['element'] = array(
                'id' => $element->id, 
                'url' => "/elements/{$element->id}", 
                'name' => $element->name, 
            );
            
            $representations[] = $representation;
        }
        return $representations;
    }
}
