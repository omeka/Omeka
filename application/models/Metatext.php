<?php
require_once 'Item.php';
require_once 'Metafield.php';
/**
 * @package Omeka
 * 
 **/
class Metatext extends Kea_Record { 
    public function setUp() {
		$this->hasOne("Item","Metatext.item_id");
		$this->hasOne("Metafield", "Metatext.metafield_id");
	}

	public function setTableDefinition() {
   	//	$this->setTableName('metatext');
        $this->hasColumn('item_id', 'integer', null, array('notnull' => true, 
                                                        'unsigned' => true));
        $this->hasColumn('metafield_id', 'integer', null, array('notnull' => true, 
                                                             'unsigned' => true));
        $this->hasColumn('text', 'string', null, array('notnull' => true, 'default'=>''));
		$this->index('item', array('fields' => array('item_id')));
		$this->index('metafield', array('fields' => array('metafield_id')));
 	}
	
	public function __toString() {
		return $this->text;
	}
}

?>