<?php

/**
 * Tag Table 
 *
 * @package Omeka
 * 
 **/
class TagTable extends Omeka_Table
{	
	public function findOrNew($name) {
		$db = get_db();
		$sql = "SELECT t.* FROM {$db->Tag} t WHERE t.name = ? LIMIT 1";
		$tag = $this->fetchObjects($sql, array($name), true);
		
		if(!$tag) {
			$tag = new Tag;
			$tag->name = $name;
		}
		
		return $tag;
	}

	/**
	 * Retrieve a certain number of tags
	 *
	 * @param int limit
	 * @param bool alphabetical order
	 * @param bool ordered by count
	 * @param bool ordered by most recent
	 * @param Item only tags from this Item
	 * @param User only tags from this User
	 * @return Doctrine_Collection tags
	 **/
	public function findBy($params=array(), $for=null)
	{
		$defaults = array(/*
			'limit'=>100,
		*/	
							'alpha'=>false,
							'recent'=>false,
							'mostToLeast'=>false,
							'leastToMost'=>false,
							'record'=>null,
							'entity'=>null,
							'user'=>null,
							'return'=>'array');

		$params = array_merge($defaults, $params);

		$select = new Omeka_Select;
		
		$db = get_db();
		
		$select->from("$db->Tag t", 't.*, COUNT(t.id) as tagCount')
				->innerJoin("$db->Taggings tg", "tg.tag_id = t.id");

		if(($record = $params['record']) and ($record instanceof Omeka_Record) ) {
			if($record->exists()) {
				$record_id = $record->id;
				$select->where("tg.relation_id = ?", $record_id);
			
				if(empty($for)) {
					$select->where("tg.type = ?", get_class($record));
				}
			}
			//A non-persistent record has no tags, so return emptiness
			else {
				return array();
			}
		}
	
		if(!empty($for)) {
			$select->where("tg.type = ?", (string) $for);
		}
		
		if($user = $params['user']) {
			$select->innerJoin("$db->Entity e", "e.id = tg.entity_id");
			$select->innerJoin("$db->User u", "u.entity_id = e.id");
			$select->where("u.id = ?", (int) $user);
		}
		elseif($entity = $params['entity']) {
			$select->innerJoin("$db->Entity e", "e.id = tg.entity_id");
			$select->where("e.id = ?", ($entity instanceof Entity) ? $entity->id : $entity );
		}

		if($params['return'] != 'count') {
				if((bool) $params['recent']) {
					$select->order('tg.time DESC');
				}elseif($alpha) {
					$select->order('t.name ASC');
				}	
				elseif((bool) $params['mostToLeast']) {
					$select->order('tagCount DESC');
				}elseif(isset($params['leastToMost'])) {
					$select->order('tagCount ASC');
				}	
		}

		//Showing tags related to items
		if($for == 'Item') {
			
			$select->innerJoin("$db->Item i", "i.id = tg.relation_id");

			//If the 'public' switch has been set
			if(array_key_exists('public', $params)) {
				$public = $params['public'];
				
				//Public is set to true, so show only public items
				if($public === true) {
					$select->where('i.public = 1');
				}
				elseif($public === false) {
					
					//Apply the permissions checks to make sure no one is cheating
					new ItemPermissions($select);
					
					$select->where('i.public = 0');
				}
				
			}
			//If they have not specified whether tags should be public or not, then apply perms check
			else {
				new ItemPermissions($select);
			}
		}	

		if($params['return'] == 'count') {
			$select->resetFrom("$db->Tag t", 'COUNT(DISTINCT(t.id))');

			return (int) $db->fetchOne((string) $select);
		}

		//Limit should always be 
		if($limit = (int) $params['limit']) {
			$select->limit($limit);
		}
		
		$select->group("t.id");


//	debug_print_backtrace();		
//echo $select;exit;
	
		if($params['return'] == 'object') {
			$tags = $this->fetchObjects($select);
		}
		else {
			$bind = null;
			$tags = $db->query((string) $select, null)->fetchAll();
		}

		if(!$tags) {
			return array();
		}
		return $tags;
	}
	
	protected function getTagSelectSQL()
	{
		$select = new Omeka_Select;
		
		$db = get_db();
		
		$select->from("$db->Tag t", 't.*, COUNT(t.id) as tagCount')
				->innerJoin("$db->Taggings tg", "tg.tag_id = t.id")
				->group('t.id');
				
		return $select;
	}
	
	/**
	 * Overloaded as a wrapper for findBy()
	 *
	 * @return mixed
	 **/
	public function findAll($for=null, $params=array())
	{
		$params = array_merge(array('return'=>'object'), $params);
		
		return $this->findBy($params, $for);
	}
	
	public function find($id) 
	{
/*
		//make the where statement
		$id = (array) $id;
		$where = array();
		foreach ($id as $val) {
			$where[] = "t.id = ?";
		}
		$where = join(' OR ', $where);
*/	
		
		if(!$id) {
			throw new Exception( 'ID must be passed when retrieving objects from the database' );
		}
		
		$select = $this->getTagSelectSQL();
		
		$select->where("t.id = ?", (int) $id)->limit(1);
		
		$tags = $this->fetchObjects($select, null, true);
							
		return $tags;
	}
	
	protected function getTagListWithCount($user_id=null) {
			$select = new Omeka_Select;
			$select->from('tags t', 't.*, (COUNT(tg.id) AS tagCount')
					->joinLeft('taggings tg', 'tg.tag_id = t.id')
					->joinLeft('entities e', 'e.id = tg.entity_id')
					->joinLeft('users u', 'u.entity_id = e.id')
					->group('t.id')
					->having('tagCount > 0');
			
			
			if($user_id) {
				//This user can only edit their own tags
				$select->where('u.id = ?', $user_id);
				$tags = $select->execute()->fetchAll();
			}else {
				//This user can edit everyone's tags
				$tags = $select->execute()->fetchAll();
			}
			
			return $tags;		
	} 
} // END class TagTable extends Omeka_Table

?>