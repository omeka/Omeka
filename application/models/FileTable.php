<?php 
/**
* 
*/
class FileTable extends Omeka_Table
{
	protected $_target = 'File';
	
	public function getRandomFileWithImage($item_id)
	{		
		$db = get_db();
		
		$sql = "SELECT f.* FROM $db->File f WHERE f.item_id = ? AND f.has_derivative_image = 1 ORDER BY RAND() LIMIT 1";

		$file = $this->fetchObjects($sql, array($item_id), true);

		return $file;
	}
	
	public function find($id)
	{
		$db = get_db();
		
		$select = new Omeka_Select;
		
		$select->from("$db->File f", "f.*");
		$select->innerJoin("$db->Item i", "i.id = f.item_id");
				
		$select->where("f.id = ?");
		$select->limit(1);
		
		new ItemPermissions($select);
		
		return $this->fetchObjects($select, array($id), true);
	}
	
	public function findByItem($item_id)
	{
		$db = get_db();
		
		$sql = "SELECT f.* FROM $db->File f WHERE f.item_id = ?";
		
		return $this->fetchObjects($sql, array($item_id));
	}
}
 
?>
