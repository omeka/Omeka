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
     * @var array Cache of elements
     */
    protected $_elementsCache = array();
    
    /**
     * @var array Cache of element sets
     */
    protected $_elementSetsCache = array();
    
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
     * Get the absolute URL to the passed resource.
     * 
     * @param string $uri The full resource URI
     * @return string
     */
    public function getResourceUrl($uri)
    {
        // Prepend a slash if not already.
        if ('/' != $uri[0]) {
            $uri = "/$uri";
        }
        return WEB_ROOT . "/api$uri";
    }
    
    /**
     * Get representations of element texts belonging to a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @return array
     */
    protected function getElementTextRepresentations(Omeka_Record_AbstractRecord $record)
    {
        $representations = array();
        
        foreach ($record->getAllElementTexts() as $elementText) {
            
            // Cache information about elements and element sets to avoid 
            // unnecessary database queries.
            if (!isset($this->_elementsCache[$elementText->element_id])) {
                $element = get_db()->getTable('Element')->find($elementText->element_id);
                $this->_elementsCache[$element->id] = array(
                    'id' => $element->id, 
                    'element_set_id' => $element->element_set_id, 
                    'name' => $element->name, 
                );
            }
            $element = $this->_elementsCache[$elementText->element_id];
            if (!isset($this->_elementSetsCache[$element['element_set_id']])) {
                $elementSet = get_db()->getTable('ElementSet')->find($element['element_set_id']);
                $this->_elementSetsCache[$elementSet->id] = array(
                    'id' => $elementSet->id, 
                    'name' => $elementSet->name, 
                );
            }
            $elementSet = $this->_elementSetsCache[$element['element_set_id']];
            
            // Build the representation.
            $representation = array(
                'html' => (bool) $elementText->html, 
                'text' => $elementText->text, 
                'element_set' => array(
                    'id' => $elementSet['id'], 
                    'url' => $this->getResourceUrl("/element_sets/{$elementSet['id']}"), 
                    'name' => $elementSet['name'], 
                ), 
                'element' => array(
                    'id' => $element['id'], 
                    'url' => $this->getResourceUrl("/elements/{$element['id']}"), 
                    'name' => $element['name'], 
                )
            );
            $representations[] = $representation;
        }
        
        return $representations;
    }
}
