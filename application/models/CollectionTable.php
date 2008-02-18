<?php 
/**
* @todo Maybe refactor these Omeka_Table classes to automatically check 
* for a permissions class and load that w/o having to manually override the methods
*/
class CollectionTable extends Omeka_Table
{
	public function findAll()
	{
		$db = get_db();
		
		$select = new Omeka_Select;
		
		$select->from("$db->Collection c");
		
		new CollectionPermissions($select);
		
		return $this->fetchObjects($select);
	}
	
	public function count()
	{
		$db = get_db();
		
		$select = new Omeka_Select;
		
		$select->from("$db->Collection c", "COUNT(DISTINCT(c.id))");
		
		new CollectionPermissions($select);
		
		return $db->fetchOne($select);
	}
	
	public function findRandomFeatured()
	{
	    $db = get_db();
	    
	    $select = new Omeka_Select;
	    
	    $select->from("$db->Collection c")->where("c.featured = 1")->order("RAND()")->limit(1);
	    
	    return $this->fetchObjects($select, array(), true);
	}
}
 
?>
