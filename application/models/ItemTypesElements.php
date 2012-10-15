<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An item type element and its metadata.
 * 
 * @package Omeka\Record
 */
class ItemTypesElements extends Omeka_Record_AbstractRecord
{
    public $item_type_id;
    public $element_id;
    public $order;
}
