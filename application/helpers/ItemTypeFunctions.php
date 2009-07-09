<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage GeneralHelpers
 **/
 
 /**
  * Retrieve a full set of ItemType objects currently available to Omeka.
  * 
  * Keep in mind that the $params and $limit arguments are in place for the sake
  * of consistency with other data retrieval functions, though in this case
  * they don't have any effect on the number of results returned.
  * 
  * @since 0.10
  * @param array $params
  * @param integer $limit
  * @return array
  **/
 function get_item_types($params = array(), $limit = 10)
 {
     return get_db()->getTable('ItemType')->findAll();
 }
 
 /**
  * Retrieve the set of values for item type elements.
  * @param Item|null Check for this specific item record (current item if null).
  * @return array
  **/
 function item_type_elements($item=null)
 {
     if (!$item) {
         $item = get_current_item();
     }
     $elements = $item->getItemTypeElements();
     foreach ($elements as $element) {
         $elementText[$element->name] = item(ELEMENT_SET_ITEM_TYPE, $element->name);
     }
     return $elementText;
 }