<?php
require_once 'Metafield.php' ;
require_once 'TypesMetafields.php' ;
/**
 * @package Omeka
 * 
 **/
class Type extends Kea_Record { 
    protected $error_messages = array(	'name' => array('notblank' => 'Type name must not be blank.'));

	public function setUp() {
		//This should be 'ownsMany' to set up the foreign key cascade delete, but it won't work with many-to-many aggregates (Doctrine_Exception)
		$this->hasMany("Metafield as Metafields", "TypesMetafields.metafield_id");
	}

	public function setTableDefinition() {
   		$this->setTableName('types');
		$this->hasColumn("name","string", 200, 'notblank|unique');
		$this->hasColumn("description","string", null);
 	}

	public function hasMetafield($name) {
		foreach( $this->Metafields as $metafield )
		{
			if($metafield->name == $name) return true;
		}
		return false;
	}
}

?>