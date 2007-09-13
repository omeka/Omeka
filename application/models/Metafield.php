<?php
require_once 'Type.php';
require_once 'Plugin.php';
require_once 'Metatext.php';
require_once 'TypesMetafields.php';
require_once 'MetafieldTable.php';
require_once 'MetafieldMetatext.php';
/**
 * @package Omeka
 * 
 **/
class Metafield extends Kea_Record { 
	
	protected $error_messages = array(	'name' => array('notblank' => 'Metafield name must not be blank', 'unique' => 'Metafield name must be different than existing metafield names'));

	public function setUp() {
		//Replace with a join table
		$this->hasMany("Type as Types", "TypesMetafields.type_id");
		$this->hasOne("Plugin", "Metafield.plugin_id");
		$this->ownsMany("MetafieldMetatext as Metatext", "MetafieldMetatext.metafield_id");
		$this->ownsMany("TypesMetafields", "TypesMetafields.metafield_id");
//		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
	}

	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
   		$this->setTableName('metafields');
		
		$this->hasColumn("name", "string", 255, array('notnull' => true, 'unique'=>true, 'notblank'=>true));
     	$this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
		$this->hasColumn("plugin_id", "integer");
		$this->index('plugin', array('fields'=>array('plugin_id')));
 	}
	
	public function delete()
	{
		fire_plugin_hook('delete_metafield', $this);
		
		$id = (int) $this->id;
		
		$delete = "DELETE types_metafields, metatext, metafields FROM metafields
		LEFT JOIN types_metafields ON types_metafields.metafield_id = metafields.id
		LEFT JOIN metatext ON metatext.metafield_id = metafields.id
		WHERE metafields.id = $id;";
		
		$this->execute($delete);
	}
	
	public static function names($prefix=true) {
		$conn = Doctrine_Manager::getInstance()->connection();
		
		$res = $conn->execute("SELECT m.name FROM metafields m ORDER BY m.name DESC");
		
		$rows = $res->fetchAll();
		
		$names = array();
		
		foreach ($rows as $row) {
			$key = $prefix ? 'metafield_' . $row['name'] : $row['name'];
			$names[$key] = $row['name'];
		}
		
		return $names;
	}
}


?>