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
abstract class Omeka_Record_Api_AbstractRecordAdapter implements Omeka_Record_Api_RecordAdapterInterface
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
     * Set data to a record during a POST request.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        $recordType = get_class($record);
        throw new Omeka_Controller_Exception_Api("The \"$recordType\" API record adapter does not implement setPostData");
    }
    
    /**
     * Set data to a record during a PUT request.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        $recordType = get_class($record);
        throw new Omeka_Controller_Exception_Api("The \"$recordType\" API record adapter does not implement setPutData");
    }
    
    /**
     * Get representations of element texts belonging to a record.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @return array
     */
    public function getElementTextRepresentations(Omeka_Record_AbstractRecord $record)
    {
        return (bool) get_option('api_filter_element_texts')
            ? $this->_getFilteredElementTextRepresentations($record)
            : $this->_getUnfilteredElementTextRepresentations($record);
    }
    
    /**
     * Set element text data to a record.
     * 
     * The record must initialize the ElementText mixin.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param mixed $data
     */
    public function setElementTextData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (!isset($data->element_texts) || !is_array($data->element_texts)) {
            return;
        }
        $elementTexts = array();
        foreach ($data->element_texts as $et) {
            if (!is_object($et)) {
                continue;
            }
            $elementText = array();
            if (isset($et->element->id)) {
                $elementText['element_id'] = $et->element->id;
            }
            if (isset($et->html)) {
                $elementText['html'] = $et->html;
            }
            if (isset($et->text)) {
                $elementText['text'] = $et->text;
            }
            $elementTexts[] = $elementText;
        }
        $record->addElementTextsByArray($elementTexts);
        $record->setReplaceElementTexts();
    }
    
    /**
     * Get representations of tags belonging to a record.
     * 
     * The record must initialize the Tag mixin.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @return array
     */
    public function getTagRepresentations(Omeka_Record_AbstractRecord $record)
    {
        $tags = array();
        foreach ($record->getTags() as $tag) {
            $tags[] = array(
                'id' => $tag->id, 
                'url' => $this->getResourceUrl("/tags/{$tag->id}"), 
                'name' => $tag->name, 
                'resource' => 'tags', 
            );
        }
        return $tags;
    }
    
    /**
     * Set tag data to a record.
     * 
     * The record must initialize the Tag mixin.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @param mixed $data
     */
    public function setTagData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (!isset($data->tags) || !is_array($data->tags)) {
            return;
        }
        $tags = array();
        foreach ($data->tags as $tag) {
            if (!is_object($tag)) {
                continue;
            }
            $tags[] = $tag->name;
        }
        $record->applyTags($tags);
    }
    
    /**
     * Get the absolute URL to the passed resource.
     * 
     * @param string $uri The full resource URI
     * @return string
     */
    public static function getResourceUrl($uri)
    {
        // Prepend a slash if not already.
        if ('/' != $uri[0]) {
            $uri = "/$uri";
        }
        return WEB_ROOT . "/api$uri";
    }
    
    /**
     * Format a date string as an ISO 8601 date, UTC timezone.
     * 
     * @param string $date
     * @return string
     */
    public static function getDate($date)
    {
        $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format('c');
    }

    /**
     * Get unfiltered representations of element texts belonging to a record.
     *
     * Note the HTML flag in the representation. This indicates to the consumer
     * that the representation is unfiltered.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @return array
     */
    protected function _getUnfilteredElementTextRepresentations(Omeka_Record_AbstractRecord $record)
    {
        $representations = array();

        // Get the record's element texts from the ElementText mixin, as opposed
        // to the AllElementTexts view helper.
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
                    'resource' => 'element_sets', 
                ), 
                'element' => array(
                    'id' => $element['id'], 
                    'url' => $this->getResourceUrl("/elements/{$element['id']}"), 
                    'name' => $element['name'], 
                    'resource' => 'elements', 
                )
            );
            $representations[] = $representation;
        }
        
        return $representations;
    }

    /**
     * Get filtered representations of element texts belonging to a record.
     *
     * Note the lack of the HTML flag in the representation. This indicates to
     * the consumer that the representation is filtered through the
     * display_elements and array('Display',...) element texts filters.
     * 
     * @param Omeka_Record_AbstractRecord $record
     * @return array
     */
    protected function _getFilteredElementTextRepresentations(Omeka_Record_AbstractRecord $record)
    {
        $representations = array();
        
        // Get the record's element texts from the AllElementTexts view helper,
        // as opposed to the ElementText mixin.
        $elementTexts = get_view()->allElementTexts($record, array(
            'return_type' => 'array',
            'show_empty_elements' => false,
        ));
        
        foreach ($elementTexts as $elementSetName => $elements) {
            
            // Account for item types.
            if (0 === substr_compare($elementSetName, ElementSet::ITEM_TYPE_NAME,
                -strlen(ElementSet::ITEM_TYPE_NAME), strlen(ElementSet::ITEM_TYPE_NAME))
            ) {
                $elements = $elementTexts[$elementSetName];
                $elementSetName = ElementSet::ITEM_TYPE_NAME;
            }
            
            foreach ($elements as $elementName => $texts) {
                $element = $record->getElement($elementSetName, $elementName);
                
                foreach ($texts as $text) {
                    
                    // Build the representation.
                    $representation = array(
                        'text' => $text,
                        'element_set' => array(
                            'id' => $element->element_set_id,
                            'url' => $this->getResourceUrl("/element_sets/{$element->element_set_id}"),
                            'name' => $elementSetName,
                            'resource' => 'element_sets',
                        ),
                        'element' => array(
                            'id' => $element->id,
                            'url' => $this->getResourceUrl("/elements/{$element->id}"),
                            'name' => $elementName,
                            'resource' => 'elements',
                        )
                    );
                    
                    $representations[] = $representation;
                }
            }
        }
        
        return $representations;
    }
}
