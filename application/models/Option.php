<?php
/**
 * @package Omeka
 * @author Kris Kelly
 **/
class Option extends Kea_Record { 
    public function setTableDefinition() {
		$this->setTableName('options');
		
        $this->hasColumn("name","string",30, "unique|notnull");
        $this->hasColumn("value","string");
    }
}
?>