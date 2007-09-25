<?php
/**
 * @package Omeka
 * 
 **/
class Option extends Omeka_Record { 
    public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName('options');
		
        $this->hasColumn("name", "string", 30, "unique|notnull");
        $this->hasColumn("value","string");
    }

	public function __toString() {
		return $this->value;
	}
}
?>