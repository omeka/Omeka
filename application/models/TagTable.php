<?php

/**
 * Tag Table 
 *
 * @package Sitebuilder
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
} // END class TagTable extends Doctrine_Table

?>