<?php 
/**
* 
*/
class UserTable extends Omeka_Table
{
	
	/**
	 * Very similar in principle to the Item_Table::getItemSelectSQL()
	 *
	 * @see Item_Table::getItemSelectSQL()
	 * @return Omeka_Select
	 **/
	private function getUserSelectSQL()
	{
		$select = new Omeka_Select;
		
		$db = get_db();
		
		$select->from("$db->User u", "
					u.id,
					u.username,
					u.password,
					u.active,
					u.role,
					u.entity_id, 
					e.first_name, 
					e.middle_name, 
					e.last_name, 
					e.institution, 
					e.email, 
					e.`type` as entity_type")
				->innerJoin("$db->Entity e", "e.id = u.entity_id");

		return $select;
	}
	
	public function findAll()
	{
		$select = $this->getUserSelectSQL();
		
		return $this->fetchObjects($select);
	}
	
	public function find($id)
	{
		$select = $this->getUserSelectSQL();
		
		$select->where("u.id = ?")
				->limit(1);
		$user = $this->fetchObjects($select, array((int) $id), true);

		return $user;
	}
	
	public function findByEntity($entity_id)
	{
		$select = $this->getUserSelectSQL();
		
		$db = get_db();
		
		$select->where("e.id = ?")
				->limit(1);
				
		$user = $this->fetchObjects($select, array((int) $entity_id), true);
		
		return $user;
	}
	
	public function findByEmail($email)
	{
		$select = $this->getUserSelectSQL();
		
		$select->where("e.email = ?");
		
		return $this->fetchObjects($select, array($email), true);
	}
}
 
?>