<?php 
/**
* EntityTable
*/
class EntityTable extends Omeka_Table
{
	public function findUniqueOrNew($values, $other = array())
	{	
		$select = new Omeka_Select;
		$db = get_db();
		
		$select->from("$db->Entity e", "e.*");
		
		foreach ($values as $key => $value) {
			$select->where("$key = ?", $value);
		}
		
		$select->limit(1);
		
		$unique = $this->fetchObjects($select, array(), true);
		
		if(!$unique) {
			$unique = new Entity;
			$unique->setArray($values);
		}
		
		return $unique;
	}
}
 
?>
