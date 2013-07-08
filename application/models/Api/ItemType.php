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
        // Get the item type elements.
        $itemTypeElements = $record->getTable('ItemTypesElements')
            ->findBy(array('item_type_id' => $record->id, Omeka_Db_Table::SORT_PARAM => 'order'));
        $elements = array();
        foreach ($itemTypeElements as $element) {
            $elements[] = array(
                'id' => $element->element_id, 
                'url' => self::getResourceUrl("/elements/{$element->element_id}"), 
            );
        }
        
        $representation = array(
            'id' => $record->id, 
            'url' => self::getResourceUrl("/item_types/{$record->id}"), 
            'name' => $record->name, 
            'description' => $record->description, 
            'elements' => $elements, 
            'items' => array(
                'count' => $record->getTable('Item')->count(array('item_type_id' => $record->id)), 
                'url' => self::getResourceUrl("/items/?item_type={$record->id}"), 
                'resource' => 'items', 
            ), 
        );
        return $representation;
    } 
    
    /**
     * Set POST data to an item type.
     *
     * @param ItemType $data
     * @param mixed $data
     */
    public function setPostData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->name)) {
            $record->name = $data->name;
        }
        if (isset($data->description)) {
            $record->description = $data->description;
        }
        if (isset($data->elements) && is_array($data->elements)) {
            $elements = array();
            foreach ($data->elements as $element) {
                if (!is_object($element)) {
                    continue;
                }
                $elements[] = $record->getTable('Element')->find($element->id);
            }
            $record->addElements($elements);
        }
    }
    
    /**
     * Set PUT data to an item type.
     *
     * @param ItemType $data
     * @param mixed $data
     */
    public function setPutData(Omeka_Record_AbstractRecord $record, $data)
    {
        if (isset($data->name)) {
            $record->name = $data->name;
        }
        if (isset($data->description)) {
            $record->description = $data->description;
        }
        if (isset($data->elements) && is_array($data->elements)) {
            $db = get_db();
            
            // Delete the existing item type elements.
            $sql = "DELETE FROM {$db->ItemTypesElements} WHERE item_type_id = ?";
            $db->query($sql, $record->id);
            
            // Insert new item type elements.
            $elementTable = $db->getTable('Element');
            $i = 1;
            foreach ($data->elements as $element) {
                if (!is_object($element) || !isset($element->id)) {
                    continue;
                }
                // Ignore if the element does not exist.
                if (!$elementTable->exists($element->id)) {
                    continue;
                }
                $itemTypesElement = new ItemTypesElements;
                $itemTypesElement->item_type_id = $record->id;
                $itemTypesElement->element_id = $element->id;
                $itemTypesElement->order = $i;
                $itemTypesElement->save();
                $i++;
            }
        }
    }
}
