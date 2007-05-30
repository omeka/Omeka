<?php
require_once 'Metafield.php';
require_once 'PluginTable.php';
/**
 * Used for plugin storage in the database
 *
 * @package default
 * 
 **/
class Plugin extends Doctrine_Record
{
	public function setUp() {
		$this->ownsMany("Metafield as Metafields", "Metafield.plugin_id");
	}
	
 	public function setTableDefinition() {
		$this->setTableName("plugins");
       	$this->hasColumn('name', 'string', 255, array('notnull' => true, 'unique'=>true, 'notblank'=>true));
        $this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('author', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('config', 'array', null);
        $this->hasColumn('active', 'boolean', null, array('default'=>'0', 'notnull' => true));		
		$this->index('active', array('fields'=>array('active')));
	}
} // END class Location extends Kea_Record


?>