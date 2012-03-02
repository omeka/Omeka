<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 * @access private
 */

/**
 * Helper used to retrieve metadata for an item.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @see item()
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_View_Helper_ItemMetadata extends Omeka_View_Helper_RecordMetadata
{
    public function itemMetadata(Item $item,
                         $elementSetName,
                         $elementName = null,
                         $options     = array())
    {
        return $this->_get($item, $elementSetName, $elementName, $options);
    }

    /**
     * Retrieve a special value of an item that does not correspond to an
     * Element record. Examples include the database ID of the item, the
     * name of the item type, the name of the collection, etc.
     *
     * Available fields include:
     *      <ul>
     *          <li>id</li>
     *          <li>item type name</li>
     *          <li>date added</li>
     *          <li>date modified</li>
     *          <li>collection name</li>
     *          <li>featured</li>
     *          <li>public</li>
     *          <li>permalink</li>
     *      </ul>
     * @param Item
     * @param string
     * @return mixed
     */
    protected function _getRecordMetadata($item, $specialValue)
    {
        switch (strtolower($specialValue)) {
            case 'id':
                return $item->id;
            case 'item type name':
                if ($type = $item->Type) {
                    return $type->name;
                } else {
                    return null;
                }
            case 'date added':
                return $item->added;
            case 'date modified':
                return $item->modified;
            case 'collection name':
                if ($collection = $item->Collection) {
                    return $collection->name;
                } else {
                    return null;
                }
            case 'featured':
                return $item->featured;
            case 'public':
                return $item->public;
            case 'permalink':
                return abs_item_uri($item);
            default:
                throw new Exception(__("'%s' is an invalid special value.", $specialValue));
        }
    }
}
