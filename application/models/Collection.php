<?php
/**
 * @package Omeka
 **/
class Collection extends Kea_Record { 
    public function setUp() {
		$this->hasMany('Item as Items', 'Item.collection_id');
	}
	
	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName('collections');
        $this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank'=>true));
        $this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('public', 'boolean', null, array('notnull' => true));
        $this->hasColumn('featured', 'boolean', null, array('notnull' => true));
        $this->hasColumn('collector', 'string', null, array('notnull' => true, 'default'=>''));
    }
}

?>