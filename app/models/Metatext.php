<?php

class Metatext extends Kea_Domain_Record { 
    public function setTableDefinition() {
   	//	$this->setTableName('metatext');

		$this->hasColumn("text","string", null);
 	}
}

?>