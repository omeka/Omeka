<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage GeneralHelpers
 */

/**
 * Retrieve the set of item types that are being looped.
 *
 * @since 1.1
 * @return array
 */
function get_item_types_for_loop()
{
    return __v()->itemtypes;
}

/**
 * @since 1.1
 * @param array $itemtypes Set of item type records to loop.
 * @return void
 */
function set_item_types_for_loop($itemtypes)
{
    __v()->itemtypes = $itemtypes;
}

/**
 * Loops through item typees assigned to the current view.
 *
 * @since 1.1
 * @return mixed The current item type in the loop.
 */
function loop_item_types()
{
    return loop_records('item_types', get_item_types_for_loop(), 'set_current_item_type');
}

/**
 * @since 1.1
 * @param ItemType
 * @return void
 */
function set_current_item_type($itemtype)
{
    __v()->itemtype = $itemtype;
}

/**
 * @since 1.1
 * @return ItemType|null
 */
function get_current_item_type()
{
    return __v()->itemtype;
}

/**
 * Determine whether there are any item types to loop through.
 *
 * @since 1.0
 * @see has_items_for_loop()
 * @return boolean
 */
function has_item_types_for_loop()
{
    $view = __v();
    return $view->itemtypes && count($view->itemtypes);
}

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
 */
function get_item_types($params = array(), $limit = 10)
{
    return get_db()->getTable('ItemType')->findAll();
}

/**
 * Retrieve the set of values for item type elements.
 * @param Item|null Check for this specific item record (current item if null).
 * @return array
 */
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
