<?php
/**
 * @package Omeka
 * @author Kris Kelly
 **/
class Collection extends Kea_Record { 
    public function setTableDefinition() {
		$this->setTableName('collections');
		
        $this->hasColumn("name","string",500, "unique|notnull");
        $this->hasColumn("description","string", null);
        $this->hasColumn("active","boolean", 1);
        $this->hasColumn("featured","boolean", 1);
		$this->hasColumn("collector", "string", null);
    }
}

?>