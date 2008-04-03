<?php
require_once 'TaggingsTable.php';
/**
 * Taggings
 * @package: Omeka
 */
class Taggings extends Omeka_Record
{
	public $relation_id;
	public $tag_id;
	public $entity_id;
	public $type;
	public $time;
}

?>