<?php

/**
 * Tag Table 
 *
 * @package Omeka
 * 
 **/
class TagTable extends Doctrine_Table
{	
	public function findOrNew($name) {
		$result = $this->findBySql('name = ? LIMIT 1', array($name))->getFirst();
		if(!$result) {
			$tag = new Tag();
			$tag->name = $name;
			return $tag;
		} else {
			return $result;
		}
	}

	/**
	 * DUPLICATED from the ItemTable class...too lazy to do anything else for now
	 *
	 * @return void
	 **/
	protected function getCountFromSelect($select)
	{		
		//Grab the total number of items in the table(as differentiated from the result count)
		$countQuery = clone $select;
		$countQuery->resetFrom(array('Tag', 't'), 'COUNT(DISTINCT(t.id))');
		$total_items = $countQuery->fetchOne();
		if(!$total_items) $total_items = 0;
		return $total_items;
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
	public function findBy($params=array(), $for=null, $returnCount=false)
	{
		$defaults = array(	'limit'=>100,
							'alpha'=>false,
							'recent'=>false,
							'mostToLeast'=>false,
							'leastToMost'=>false,
							'record'=>null,
							'entity'=>null,
							'user'=>null,
							'public'=>false,
							'return'=>'array');
							
		foreach ($defaults as $k=>$v) {
			if(array_key_exists($k,$params)) {
				$$k = $params[$k];
			}else{ 
				$$k = $v;
			}
		}
		
		$select = new Omeka_Select;
		
		$select->from(array('Tag', 't'), 't.*, COUNT(t.id) as tagCount')
				->innerJoin(array('Taggings', 'tg'), "tg.tag_id = t.id");

		if($record instanceof Omeka_Record) {
			if($record->exists()) {
				$record_id = $record->id;
				$select->where("tg.relation_id = ?", $record_id);
			
				if(empty($for)) {
					$select->where("tg.type = ?", get_class($record));
				}
			}
			//A non-persistent record has no tags, so return emptiness
			else {
				return ($return == 'array') ? array() : new Doctrine_Collection('Tag');
			}
		}
	
		if(!empty($for)) {
			$select->where("tg.type = ?", (string) $for);
		}
		
		if($user) {
			$select->innerJoin(array('Entity', 'e'), "e.id = tg.entity_id");
			$select->innerJoin(array('User', 'u'), "u.entity_id = e.id");
			$select->where("u.id = ?", ($user instanceof User) ? $user->id : $user);
		}
		elseif($entity) {
			$select->innerJoin(array('Entity', 'e'), "e.id = tg.entity_id");
			$select->where("e.id = ?", ($entity instanceof Entity) ? $entity->id : $entity );
		}

		if($recent) {
			$select->order('tg.time DESC');
		}elseif($alpha) {
			$select->order('t.name ASC');
		}
		elseif($mostToLeast) {
			$select->order('tagCount DESC');
		}elseif($leastToMost) {
			$select->order('tagCount ASC');
		}
		

		
		//Showing tags related to public items
		if($public and $for == 'Item') {
			$select->innerJoin(array('Item', 'i'), "i.id = tg.relation_id");
			$select->where("i.public = 1");
		}	

		if($limit) {
			$select->limit($limit);
		}
		
				
		if($returnCount) {
			return $this->getCountFromSelect($select);
		}
		
		$select->group("t.id");
		
//echo $select;
		
		//Return Doctrine_Collection instead of an array (the slow way)
		if($return == 'object') {
			$ids = array();
			$select->resetFrom(array('Tag', 't'), "DISTINCT t.id");
			$array = $select->fetchAll();
			foreach ($array as $row) {
				$ids[] = $row['id'];
			}
			return !empty($ids) ? $this->find($ids, true) : new Doctrine_Collection('Tag');
		}
		
		return $select->fetchAll();
	}
	
	/**
	 * Overloaded as a wrapper for findBy()
	 *
	 * @return mixed
	 **/
	public function findAll($for=null, $params=array())
	{
		$params = array_merge(array(
						'limit'=>null, 'return'=>'object'), $params);
		return $this->findBy($params, $for);
	}
	
	/**
	 * Overloaded to include tagCount within all retrieved tags
	 *
	 * @param int id
	 * @return Tag
	 **/
	public function find($id, $makeCollection=false) 
	{
		//make the where statement
		$id = (array) $id;
		$where = array();
		foreach ($id as $val) {
			$where[] = "t.id = ?";
		}
		$where = join(' OR ', $where);
		
		$q = $this->createQuery()
					->select("t.*, COUNT(t.id) tagCount")
					->from('Tag t')
					->where($where, $id)
					->groupby('t.id');
		$tags = $q->execute();
					
		if((count($tags) == 1) and !$makeCollection) return $tags->getFirst();
		
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
} // END class TagTable extends Doctrine_Table

?>