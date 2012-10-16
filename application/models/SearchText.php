<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A search text and its metadata.
 * 
 * @package Omeka\Record
 */
class SearchText extends Omeka_Record_AbstractRecord
{
    public $record_type;
    public $record_id;
    public $public;
    public $title;
    public $text;
}
