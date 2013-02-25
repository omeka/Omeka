<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Record linking an Element with an ItemType.
 * 
 * @package Omeka\Record
 */
class ItemTypesElements extends Omeka_Record_AbstractRecord
{
    /**
     * ID for the ItemType being linked.
     *
     * @var int
     */
    public $item_type_id;

    /**
     * ID for the Element being linked.
     *
     * @var int
     */
    public $element_id;

    /**
     * Relative order of the Element within the ItemType.
     *
     * @var int
     */
    public $order;
}
