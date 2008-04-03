<?php
require_once 'Type.php';
require_once 'Plugin.php';
require_once 'Metatext.php';
require_once 'TypesMetafields.php';
require_once 'MetafieldTable.php';
/**
 * @package Omeka
 * 
 **/
class Metafield extends Omeka_Record { 
	
	public $name;
	public $description = '';
	public $plugin_id;
		
	protected function _delete()
	{		
		$db = get_db();
		
		//Cascade delete the metatext and the types_metafields joins
		$mt_objs = $db->getTable('Metatext')->findBySql("metafield_id = ?", array($this->id));
		
		foreach ($mt_objs as $mt_obj) {
			$mt_obj->delete();
		}
		
		$tm_objs = $db->getTable('TypesMetafields')->findBySql('metafield_id = ?', array($this->id));
		
		foreach ($tm_objs as $tm_obj) {
			$tm_obj->delete();
		}
/*
		$id = (int) $this->id;
		$delete = "DELETE types_metafields, metatext, metafields FROM metafields
		LEFT JOIN types_metafields ON types_metafields.metafield_id = metafields.id
		LEFT JOIN metatext ON metatext.metafield_id = metafields.id
		WHERE metafields.id = $id;";
		
		$db->exec($delete);
*/	
	}
	
	protected function _validate()
	{
		if(empty($this->name)) {
			$this->addError('name', 'Metafield name must not be blank');
		}
		
		if(!$this->fieldIsUnique('name')) {
			$this->addError('name', 'Metafield name must be different than existing metafield names');
		}
		
		if(!($this->plugin_id === NULL) and !is_numeric($this->plugin_id)) {
			$this->addError('plugin_id', "Metafield was incorrectly associated with a plugin");
		}
	}
	
	public static function names($prefix=true) {
		$db = get_db();
		
		$res = $db->query("SELECT m.name FROM $db->Metafield m ORDER BY m.name DESC");
		
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