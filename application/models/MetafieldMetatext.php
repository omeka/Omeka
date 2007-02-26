<?php
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Metatext.php';
/**
 * Inherited off of Metatext to support using item_id as collection key
 *
 * @package Omeka
 **/
class MetafieldMetatext extends Metatext
{	
	public function setUp() {
		$this->hasOne("Item","MetafieldMetatext.item_id");
		$this->hasOne("Metafield", "MetafieldMetatext.metafield_id");
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'item_id');
	}
} // END class ItemMetatext extends Metatext

?>