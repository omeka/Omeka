<?php

class Tag extends Kea_Domain_Record { 
    public function setTableDefinition() {
		$this->setTableName('tags');
		
   		$this->hasColumn("name","string", null);
 	}
}

?>