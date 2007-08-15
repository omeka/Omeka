<?php 
/**
* 
*/
class UserTable extends Doctrine_Table
{

	public function findByEmail($email)
	{
		
		$query = new Doctrine_Query;
		$query->parseQuery("SELECT u.* FROM User u JOIN u.Entity en WHERE en.email = ?");
		
		return $query->execute(array($email))->getFirst();

	}
}
 
?>