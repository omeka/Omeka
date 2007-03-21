<?php

/**
 * Special derivative class to ensure unique entries in the join table
 *
 * @package Omeka
 * @author Kris Kelly
 **/
abstract class Kea_JoinRecord extends Kea_Record
{
	
	/**
	 * Determine the uniqueness of a join table record based on the combination of its relational indices
	 *
	 * @return bool
	 **/
	public function isUnique() {
		$columns = $this->getKeyColumns();
		$where = array();
		foreach ($columns as $column) {
			$where[$column]= "$column = {$this->$column} ";
		}
		$result = $this->getTable()->findBySql( implode(' AND ', $where) )->getFirst();
		return (!$result || ($result->obtainIdentifier() == $this->obtainIdentifier()));
	}
	
	public function validate() {
		if(!$this->isUnique()) {
				$this->getErrorStack()->add('duplicate', 'unique');
		}	
	}
	
	public function getKeyColumns() {
		$columns = $this->getTable()->getColumns();	
		$keys = array();
		foreach( $columns as $key => $column )
		{
			if( $column[0] == 'integer' && !isset($column[2]['autoincrement']) && isset($column[2]['notnull'])) {
				$keys[] = $key;
			}
		}
		return $keys;
	}
	
} // END class Kea_JoinRecord extends Kea_Record

?>