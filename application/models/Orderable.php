<?php 
/**
* 
*/
class Orderable extends Omeka_Record_Module
{
	public function __construct($record, $childClass, $childFk, $childPluralized)
	{
		$this->record = $record;
		$this->childClass = $childClass;
		$this->childFk = $childFk;
		$this->pluralized = $childPluralized;
	}
	
	public function loadOrderedChildren()
	{
		$id = (int) $this->record->id;
		$db = get_db();
		$target = $this->childClass;
				
		$sql = "SELECT s.* FROM {$db->$target} s WHERE s.{$this->childFk} = $id ORDER BY s.`order` ASC";

		$children = $this->getTable($target)->fetchObjects($sql);

		//Now index them according to their order
		$indexed = array();
		
		foreach ($children as $child) {
			$indexed[(int) $child->order] = $child;
		}
		
		return $indexed;
	}
	
	public function afterSaveForm($post)
	{
		$form = $post[$this->pluralized];
		
		if(!empty($form)) {
		
			$children = $this->loadOrderedChildren();
			
			//Change the order of the sections
			foreach ($form as $key => $entry) {
				$child = $children[$key];
				$child->order = $entry['order'];				
				$child->save();
			}
		}				
	}
	
	/**
	 * This will realign the child nodes in ascending natural order after one is removed
	 *
	 * @return void
	 **/
	public function reorderChildren()
	{
		//Retrieve all section IDs in ascending order, then update 
		$db = get_db();
		
		$target = $this->childClass;
		
		$table = $db->$target;
		
		$sql = "SELECT s.id, s.order FROM $table s WHERE s.{$this->childFk} = ? ORDER BY s.order ASC";
		
		$res = $db->query($sql, array($this->record->id));
		
		$i = 1;
		$update = "UPDATE $table s SET s.order = ? WHERE s.id = ?";
		
		foreach ($res as $row) {
			$child_id = (int) $row['id'];
			$db->exec($update, array($i, $child_id));
			$i++;
		}
	}
	
	public function addChild(Omeka_Record $child)
	{
		if(!$this->record->exists()) {
			throw new Exception( 'Cannot add a child to a record that does not exist yet!' );
		}
		
		if(!($child instanceof $this->childClass)) {
			throw new Exception( 'Child must be an instance of "'.$this->childClass.'"' );
		}
		
		$fk = $this->childFk;
		
		$child->$fk = $this->record->id;
		
		$new_order = $this->getChildCount() + 1;
		
		$child->order = $new_order;
		
		return $child;
	}
	
	public function getChildCount()
	{
		$db = get_db();
		
		$target = $this->childClass;
		
		$sql = "SELECT COUNT(*) FROM {$db->$target} WHERE $this->childFk = ?";
		return $db->fetchOne($sql, array($this->record->id));
	}
}
 
?>
