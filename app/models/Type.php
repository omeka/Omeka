<?php

class Type extends Kea_Domain_Record { 
    public function setTableDefinition() {
   		$this->setTableName('types');

		$this->hasColumn("description","string", null);
 	}
}

?>