<?php

class Metafield extends Kea_Domain_Record { 
    public function setTableDefinition() {
   		$this->setTableName('metafields');

		$this->hasColumn("description","string", null);
 	}
}


?>