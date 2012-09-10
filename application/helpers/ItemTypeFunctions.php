<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage GeneralHelpers
 */

/**
 * Retrieve the set of values for item type elements.
 * @param Item|null Check for this specific item record (current item if null).
 * @return array
 */
function item_type_elements($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    $elements = $item->getItemTypeElements();
    foreach ($elements as $element) {
        $elementText[$element->name] = metadata($item, array(ELEMENT_SET_ITEM_TYPE, $element->name));
    }
    return $elementText;
}
