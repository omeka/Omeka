<?php
/**
 * Used for plugin storage in the database
 *
 * @package default
 * @author Kris Kelly
 **/
class Plugin extends Doctrine_Record
{
 	public function setTableDefinition() {
		$this->setTableName("plugins");
		$this->hasColumn("name", "string", 400, "unique");
		$this->hasColumn("path", "string");
		$this->hasColumn("config", "string");
		$this->hasColumn("active", "boolean");
	}
} // END class Location extends Kea_Record


?>