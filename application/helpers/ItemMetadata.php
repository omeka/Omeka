<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 */

/**
 * Helper used to retrieve metadata for an item.
 *
 * @see item()
 * @package Omeka_ThemeHelpers
 * @subpackage Omeka_View_Helper
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
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
     *          <li>collection name</li>
     *          <li>featured</li>
     *          <li>public</li>
     *          <li>permalink</li>
     *      </ul>
     * @param Item
     * @param string
     * @return mixed
     **/
    protected function _getRecordMetadata($item, $specialValue)
    {
        switch (strtolower($specialValue)) {
            case 'id':
                return $item->id;
                break;
            case 'item type name':
                return $item->Type->name;
                break;
            case 'date added':
                return $item->added;
                break;
            case 'date modified':
                return $item->modified;
                break;
            case 'collection name':
                return $item->Collection->name;
                break;
            case 'featured':
                return $item->featured;
                break;
            case 'public':
                return $item->public;
                break;
            case 'permalink':
                return abs_item_uri($item);
            default:
                throw new Exception("'$specialValue' is an invalid special value.");
                break;
        }
    }
}
