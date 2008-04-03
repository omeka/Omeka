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
		$sql = "SELECT t.* FROM {$db->Tag} t WHERE t.name COLLATE utf8_bin LIKE ? LIMIT 1";
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
	 * @param array $params
	 * 		'sort' => 'recent', 'least', 'most', 'alpha'
	 *		'limit' => int
	 * 		'record' => instanceof Omeka_Record
	 *		'entity' => entity_id
	 *		'user' => user_id
	 *		'return' => 'array', 'object', 'count'
	 * @return mixed
	 **/
	public function findBy($params=array(), $for=null)
	{
		$defaults = array(/*
			'limit'=>100,
		*/	
							'sort'=>false,
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
				switch ($params['sort']) {
					case 'recent':
						$select->order('tg.time DESC');
						break;
					case 'alpha':
						$select->order('t.name ASC');
						break;
					case 'most':
						$select->order('tagCount DESC');
						break;
					case 'least':
						$select->order('tagCount ASC');
						break;
					default:
						break;
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
			$tags = $db->query((string) $select, array())->fetchAll();
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
		
		$tags = $this->fetchObjects($select, array(), true);
							
		return $tags;
	}
} // END class TagTable extends Omeka_Table

?>