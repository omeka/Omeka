<?php

/**
 * Tag Table 
 *
 * @package Omeka
 * @author Kris Kelly
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
	 * @todo Sort by alphabetical, by count
	 */
	public function getSome($limit = 100, $alpha = true, $count = false, $item = null, $user = null )
	{
		$query = $this->createQuery()->select('t.*, COUNT(t.id) tagCount')->from('Tag t')->limit($limit);
		$query->innerJoin('t.ItemsTags it');
		if($item) {
			$query->innerJoin('it.Item i');
			$query->where('i.id = '.$item->id);			
		}
		if($user) {
			$query->innerJoin('it.User u');
			$query->addWhere('u.id = '.$user->id);
		}
		if($alpha) {
			$query->addOrderBy('t.name asc');
		}
		if($count) {
			$query->addOrderBy('tagCount desc');
		}
		$query->groupby('t.id');
		return $query->execute();
	}
} // END class TagTable extends Doctrine_Table

?>