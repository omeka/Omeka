<?php 
/**
* TaggingsTable
*/
class TaggingsTable extends Doctrine_Table
{
	/**
	 * Current options
	 *
	 * @return void
	 **/
	public function findBy($options=array(), $for=null, $returnCount=false) 
	{
		$q = new Doctrine_Query;
		$q->parseQuery("SELECT tg.* FROM Taggings tg");
		
		if(isset($options['tag'])) {
			
			$tag = $options['tag'];
			$q->innerJoin("tg.Tag t");

			//If it is an array or a collection then loop through and create a WHERE clause
			if( is_array($tag) or ($tag instanceof Doctrine_Collection)) {
				$where = array();
				$pass = array();
				foreach ($tag as $k => $t) {
					$where[$k] = "t.name = ?";
					$pass[] = ($t instanceof Tag) ? $t->name : $t; 
				}
				
				$q->addWhere( join(" OR ", $where), $pass);
				
			}else {							
				$tag = ($tag instanceof Tag) ? $tag->name : $tag;
			
				$q->addWhere("t.name = ?", array($tag));
			}		

		}
		
		if(isset($options['entity'])) {
			$entity = $options['entity'];
			
			$q->innerJoin("tg.Entity e");
			
			$entity_id = is_numeric($entity) ? $entity : (int) $entity->id;
			
			$q->addWhere("e.id = ?", array($entity_id));
		}
		
		if(isset($options['user'])) {
			$user = $options['user'];
			
			$q->innerJoin("tg.Entity e");
			$q->innerJoin("e.User u");
			
			if(is_numeric($user)) {
				$q->addWhere("u.id = ?", array($user));
			}elseif($user instanceof User and !empty($user->id)) {
				$q->addWhere("u.id = ?", array($user->id));
			}
		}
		
		if(isset($options['record'])) {
			$record = $options['record'];
			
			$q->addWhere("tg.relation_id = ? AND tg.type = ?", array($record->id, get_class($record)));
		}
		
		if($for and !isset($options['record'])) {
			$q->addWhere("tg.type = ?", array($for) );
		}
		
//		echo $q;
				
		if($returnCount) {
			$count = $q->count();
			return $count;
		}

		return $q->execute();
	}
}
 
?>
