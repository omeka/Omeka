<?php 
/**
* 
*/
abstract class Omeka_Record_Module
{
	protected $record;
	
	public function __call($m, $a)
	{
		return call_user_func_array( array($this->record, $m), $a);
	}
	
	public function beforeSave() {}
	public function beforeUpdate() {}
	public function beforeInsert() {}
	public function afterInsert() {}
	public function afterSave() {}
	public function afterUpdate() {}
	public function afterSaveForm(&$post) {}
	public function beforeSaveForm(&$post) {}
	public function beforeDelete() {}
	public function afterDelete() {}
	public function beforeValidate() {}
	public function afterValidate() {}
}
 
?>
