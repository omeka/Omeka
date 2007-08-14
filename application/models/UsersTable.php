<?php 
/**
* 
*/
class UserTable extends Doctrine_Table
{

	public function findByEmail($email)
	{
		$query = new Doctrine_Query;
		$query->parseQuery("SELECT * FROM Entity en WHERE en.email = ?");
		
		return $query->execute(array($email));
		
	}
}
 
?>