<?php 
/**
* TaggingsTable
*/
class TaggingsTable extends Omeka_Table
{
	/**
	 * Current options
	 *
	 * @return void
	 **/
	public function findBy($options=array(), $for=null, $returnCount=false) 
	{
		$select = new Omeka_Select;
		$db = get_db();
		
		if($returnCount) {
			$select->from("$db->Taggings tg", "COUNT(DISTINCT(tg.id))");
		}else {
			$select->from("$db->Taggings tg", "tg.*");
		}
				
		if(isset($options['tag'])) {
			
			$tag = $options['tag'];
			$select->innerJoin("$db->Tag t", "t.id = tg.tag_id");
			
			if(is_array($tag)) {
				$wheres = array();
				$names = array();
				foreach ($tag as $t) {
					$name = ($t instanceof Tag) ? $t->name : $t;
					$wheres[] = "t.name = ".$db->quote($t);
				}
				$select->where( "(" . implode(' OR ', $wheres) . ")" );
			}
			else {
				$name = ($tag instanceof Tag) ? $tag->name : $tag;
		
				$select->where("t.name = ?", $name);				
			}
		}
		
		if(isset($options['entity']) or isset($options['user'])) {
			
			$select->innerJoin("$db->Entity e", "e.id = tg.entity_id");
			
			if($entity = $options['entity']) {
				
				$entity_id = (int) is_numeric($entity) ? $entity : $entity->id;
				$select->where("e.id = ?", $entity_id);
				
			}elseif($user = $options['user']) {
				
				$select->innerJoin("$db->User u", "u.entity_id = e.id");
				
				if(is_numeric($user)) {
					$select->where("u.id = ?", $user);
				}elseif($user instanceof User and !empty($user->id)) {
					$select->where("u.id = ?", $user->id);
				}
			}			
		}
			
		if(isset($options['record'])) {
			$record = $options['record'];
			
			$select->where("tg.relation_id = ?", $record->id);
			$select->where("tg.type = ?", get_class($record) );
		}
		
		if($for and !isset($options['record'])) {
			$select->where("tg.type = ?", $for );
		}
						
		if($returnCount) {
			return $db->fetchOne($select);
		}else {
			return $this->fetchObjects($select);
		}
	}
}
 
?>
