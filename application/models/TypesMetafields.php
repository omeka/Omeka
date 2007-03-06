<?php
require_once 'Type.php';
require_once 'Metafield.php';
/**
 * TypesMetafields join record
 *
 * @todo Add validation so that each combination is unique
 * @package Omeka
 * 
 **/
class TypesMetafields extends Kea_Record
{
	public function setUp() {
		$this->hasOne("Type", "TypesMetafields.type_id");
		$this->hasOne("Metafield", "TypesMetafields.metafield_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("type_id", "integer", null, "notnull");
		$this->hasColumn("metafield_id", "integer", null, "notnull");
	}
	
	/**
	 * Validate unique combinations of type_id and metafield_id (almost entirely duplicated from ItemsTags::validate)
	 *
	 * @return void
	 **/
	public function validate() {
		$preExisting = $this->getTable()->findBySql("type_id = ? AND metafield_id = ? ", array($this->type_id, $this->metafield_id));
		if($preExisting && $it = $preExisting->getFirst()) {
			//Is there a better way to compare an object with its referent in the database?
			if($it->obtainIdentifier() != $this->obtainIdentifier()) {
				$this->getErrorStack()->add('type_id', 'duplicate');
			}
			
		}
	}
} // END class TypesMetafields extends Kea_Record

?>