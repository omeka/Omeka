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
	public function findSome($params=array(),$for="Item")
	{
		$defaults = array('limit'=>100,
							'alpha'=>false,
							'recent'=>false,
							'lowToHigh'=>false,
							'highToLow'=>false,
							'item_id'=>null,
							'user_id'=>null,
							'returnType'=>'object',
							'onlyPublic'=>false,
							'exhibit_id'=>null);
							
		foreach ($defaults as $k=>$v) {
			if(array_key_exists($k,$params)) {
				$$k = $params[$k];
			}else{ 
				$$k = $v;
			}
		}
		$dql = "SELECT t.*, COUNT(t.id) tagCount FROM Tag t";
		
		$pass = array();
		$join = array();
		$where = array();
			
		//Figure out where to get the tags from
		switch (strtolower($for)) {
			case 'exhibit':
				$join['et'] = "t.ExhibitsTags it";
				break;
			case 'item':
			default:
				$join['it'] = "t.ItemsTags it";
				break;
		}
	
		
		$query = new Doctrine_Query;
		

		
		if($item_id) {
			$join['it'] = "t.ItemsTags it";
			$join['item'] = "it.Item i";
			$where['item'] = 'i.id = ?';
			$pass[] = $item_id;	
		}
		if($exhibit_id) {
			$join['et'] = "t.ExhibitsTags it";
			$join['exhibit'] = "it.Exhibit ex";
			$where['exhibit'] = 'ex.id = ?';
			$pass[] = $exhibit_id;
		}
		
		if($user_id) {
			$join['user'] = 'it.User u';
			$where['user'] = 'u.id = ?';
			$pass[] = $user_id;
		}
		if($recent) {
			$order[] = 't.id DESC';
		}elseif($alpha) {
			$order[] = 't.name ASC';
		}
		elseif($highToLow) {
			$order[] = 'tagCount DESC';
		}elseif($lowToHigh) {
			$order[] = 'tagCount ASC';
		}
		
		//Showing tags related to public items
		if($onlyPublic) {
			if(!array_key_exists('item',$join)) {
				$join['item'] = "it.Item i";
			}
			$where['public'] = "i.public = 1";
		}
		
		$dql .= (!empty($join)?" INNER JOIN ".join(' INNER JOIN ', $join):"").
				(!empty($where) ? " WHERE ".join(' AND ',$where):"").
				(!empty($order)?' ORDER BY '.join(',',$order):"");
		
		if($limit) {
			$dql .= " LIMIT $limit";
		}
		$dql .= " GROUP BY t.id ";

		$query->parseQuery($dql);

		if($returnType == 'array') {
			$res = $query->execute($pass, Doctrine::FETCH_ARRAY);
			foreach ($res as $key => $value) {
				$array[$key]['name'] = $res[$key]['t']['name'];
				$array[$key]['id'] = $res[$key]['t']['id'];
				$array[$key]['tagCount'] = $res[$key]['t'][0];
			}
			return $array;
		}else {
			return $query->execute($pass);
		}
		
		
	}
	
	/**
	 * Overloaded as a wrapper for findSome()
	 *
	 * @return mixed
	 **/
	public function findAll($params=array())
	{
		$params = array_merge(array(
						'limit'=>null,
						'returnType'=>'array'), $params);
		return $this->findSome($params);
	}
	
	/**
	 * Overloaded to include tagCount within all retrieved tags
	 *
	 * @param int id
	 * @return Tag
	 **/
	public function find($id) 
	{
		$id = (int) $id;
		return $this->createQuery()
					->select("t.*, COUNT(t.id) tagCount")
					->from('Tag t')
					->where('t.id = ?',array( $id ) )
					->groupby('t.id')
					->execute()->getFirst();
	}
} // END class TagTable extends Doctrine_Table

?>