<?php
require_once 'Item.php';
require_once 'Metafield.php';
require_once 'MetatextTable.php';
/**
 * @package Omeka
 * 
 **/
class Metatext extends Omeka_Record { 
    
	public $item_id;
	public $metafield_id;
	public $text;

	public function __toString() {
		return (string) $this->text;
	}
}

?>