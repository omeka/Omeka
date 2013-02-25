<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An entry in the site-wide fulltext search index for a record.
 * 
 * @package Omeka\Record
 */
class SearchText extends Omeka_Record_AbstractRecord
{
    /**
     * Type of this text's associated record.
     *
     * @var int
     */
    public $record_type;

    /**
     * ID of this text's associated record.
     *
     * @var int
     */
    public $record_id;

    /**
     * Whether this text is publicly accessible.
     *
     * @var int
     */
    public $public;

    /**
     * Display title for the record in search results.
     *
     * @var string
     */
    public $title;

    /**
     * Searchable text for the record.
     *
     * @var string
     */
    public $text;
}
