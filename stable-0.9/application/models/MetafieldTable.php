<?php

/**
 * MetafieldTable
 *
 * @package Omeka
 * 
 **/
class MetafieldTable extends Omeka_Table
{
	/**
	 * Used to retrieve the row ID given the name (which is unique)
	 * Called in one spot as of 12/13/07 : Item::saveMetatext()
	 *
	 * A bit more efficient than pulling down the entire row but may or may not be necessary
	 *
	 * @return int
	 **/
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