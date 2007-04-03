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
			if($this->$column instanceof Kea_Record) {
				//If the related element doesn't exist yet, there's no way that this current one isn't unique
				if(!$this->$column->exists()) $this->$column->save();
				$idNum = $this->$column->id;
			}else {
				$idNum = $this->$column;
			}
			$where[$column]= "$column = {$idNum} ";
		}
		$where = implode(' AND ', $where);
		$result = $this->getTable()->findBySql( $where )->getFirst();
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