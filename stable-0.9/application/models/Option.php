<?php
/**
 * @package Omeka
 * 
 **/
class Option extends Omeka_Record { 
    public $name;
	public $value;

	public function __toString() {
		return $this->value;
	}
	
	protected function _validate()
	{
		if(empty($this->name)) {
			$this->addError('name', 'Each option must have a name.');
		}
		
		if(!$this->fieldIsUnique('name')) {
			$this->addError('name', 'Each option must have a unique name.');
		}
	}
}
?>