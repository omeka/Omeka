<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 * @access private
 */

/**
 * Helper that writes XHTML containing metadata about an item.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @see show_item_metadata()
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_View_Helper_ItemMetadataList extends Omeka_View_Helper_RecordMetadataList
{
    /**
     * The name of the item type element set. Change this constant if the name
     * changes in the database.
     */
    const ELEMENT_SET_ITEM_TYPE = ELEMENT_SET_ITEM_TYPE;

    /**
     * Virtual constructor.
     * @param Item
     * @param array $options
     * Available options:
     * 'show_empty_elements' => bool|string Whether to show elements that
     *     do not contain text. A string will set self::$_showEmptyElements to
     *     true and set self::$_emptyElementString to the provided string.
     * 'show_element_sets' => array List of names of element sets to display.
     * 'return_type' => string 'array', 'html'.  Defaults to 'html'.
     * @since 1.0 Added 'show_element_sets' and 'return_type' options.
     * @return string
     */
    public function itemMetadataList(Item $item, array $options = array())
    {
        return $this->_getList($item, $options);
    }

    /**
     * Get an array of all element sets containing their respective elements.
     * @uses Item::getAllElementsBySet()
     * @uses Item::getItemTypeElements()
     * @return array
     */
    protected function _getElementsBySet()
    {
        $elementsBySet = parent::_getElementsBySet();

        if ($this->_record->item_type_id) {
            // Overwrite elements assigned to the item type element set with only
            // those that belong to this item's particular item type. This is
            // necessary because, otherwise, all item type elements will be shown.
            $itemTypeElementSetName = item('Item Type Name', null, array(), $this->_record) . ' ' . self::ELEMENT_SET_ITEM_TYPE;

            // Check to see if either the generic or specific Item Type element
            // set has been chosen, i.e. 'Item Type Metadata' or 'Document
            // Item Type Metadata', etc.
            if (!empty($this->_elementSetsToShow)) {
                if (in_array($itemTypeElementSetName, $this->_elementSetsToShow) or
                in_array(self::ELEMENT_SET_ITEM_TYPE, $this->_elementSetsToShow)) {
                    $elementsBySet[$itemTypeElementSetName] = $this->_record->getItemTypeElements();
                }
            }
            else {
                $elementsBySet[$itemTypeElementSetName] = $this->_record->getItemTypeElements();
            }
        }

        // Unset the existing 'Item Type' element set b/c it shows elements for all item types.
         unset($elementsBySet[self::ELEMENT_SET_ITEM_TYPE]);

        return $elementsBySet;
    }

    /**
     * @todo Could pass the name of the partial in as an argument rather than hard
     * coding it.  That would allow us to use arbitrary partials for purposes such as
     * repackaging the data for RSS/XML or other data formats.
     *
     * @param array
     * @return void
     */
    protected function _loadViewPartial($vars = array())
    {
        return common('item-metadata', $vars, 'items');
    }

    protected function _getFormattedElementText($record, $elementSetName, $elementName)
    {
        return $this->view->itemMetadata($record, $elementSetName, $elementName, 'all');
    }
}
