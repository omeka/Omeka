<?php

/**
 * MetafieldTable
 *
 * @package Omeka
 * 
 **/
class MetafieldTable extends Omeka_Table
{
	
	public function findIdFromName($name)
	{
		$db = get_db();
		$sql = "SELECT mf.id FROM $db->Metafield mf WHERE mf.name = ? LIMIT 1";
		return (int) $db->fetchOne($sql, array($name));
	}

	public function findByName($name) {
		$metafields = $this->findBySql("name = ?", array($name));
		
		return current($metafields);
	}
} // END class MetafieldTable extends Omeka_Table

?>