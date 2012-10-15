<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A tagging and its metadata.
 * 
 * @package Omeka\Record
 */
class Taggings extends Omeka_Record_AbstractRecord
{
    public $relation_id;
    public $tag_id;
    public $type;
    public $time;
}
