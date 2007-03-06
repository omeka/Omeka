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
	public function getSome($limit = 100, $alpha = true, $recent = false, $count = false, $item = null, $user = null )
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
		if($recent) {
			$query->addOrderBy('t.id desc');
		}
		
		$query->groupby('t.id');
		return $query->execute();
	}
	
	/**
	 * Overloaded as a wrapper for getSome()
	 *
	 * @return Doctrine_Collection
	 **/
	public function findAll($alpha = false, $count = false, $item = null, $user = null)
	{
		return $this->getSome($this->count(), $alpha, false, $count, $item, $user);
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