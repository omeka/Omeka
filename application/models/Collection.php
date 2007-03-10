<?php
/**
 * @todo Name field needs to be larger across all models, will be adjusted once Doctrine fixes its bugs
 * @todo fix includes to be sure Kea_Record is included
 * @package Omeka
 **/
class Collection extends Kea_Record { 
    public function setTableDefinition() {
		$this->setTableName('collections');
        $this->hasColumn("name","string",255, "unique|notnull");
        $this->hasColumn("description","string", null);
        $this->hasColumn("active","boolean", 1);
        $this->hasColumn("featured","boolean", 1);
		$this->hasColumn("collector", "string", null);
    }
}

?>