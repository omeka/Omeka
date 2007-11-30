<?php
require_once 'EntityRelationships.php';
/**
 * ItemsPeople
 * @package: Omeka
 */
class EntitiesRelations extends Omeka_Record
{
	public $entity_id;
	public $relation_id;
	public $relationship_id;
	public $type;
	public $time;
	
	protected function _validate()
	{
		if(empty($this->type)) {
			$this->addError('Joins in the EntitiesRelations table must be given a polymorphic type');
		}
		
		if(empty($this->relation_id) or empty($this->relationship_id)) {
			$this->addError('Joins in the EntitiesRelations table must be filled out entirely');
		}
	}
	
	//@todo Move this to CURRENT_TIMESTAMP() SQL
	public function beforeInsert()
	{
		$this->time = date('YmdHis');
	}
}

?>
