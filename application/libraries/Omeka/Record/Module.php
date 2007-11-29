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
	
	public function preSave() {}
	public function preUpdate() {}
	public function preInsert() {}
	public function postInsert() {}
	public function postSave() {}
	public function postUpdate() {}
	public function postSaveForm(&$post) {}
	public function preSaveForm(&$post) {}
	public function preDelete() {}
	public function postDelete() {}
	public function preValidate() {}
	public function postValidate() {}
}
 
?>
