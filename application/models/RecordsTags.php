<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Linkage between a record and a tag.
 * 
 * @package Omeka\Record
 */
class RecordsTags extends Omeka_Record_AbstractRecord
{
    /**
     * ID of the record being linked.
     *
     * @var int
     */
    public $record_id;

    /**
     * Type of the record being linked.
     *
     * @var int
     */
    public $record_type;

    /**
     * ID of the tag being linked.
     *
     * @var int
     */
    public $tag_id;

    /**
     * Timestamp when this linkage was created.
     *
     * @var string
     */
    public $time;
}
