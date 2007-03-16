<?php

/**
 * Integration between Zend_Search_Lucene and the controllers
 *
 * @todo Abstract out the SEARCH_DIR constant so that more than one index can used if necessary
 * @todo Keep an array repository of unique identifiers for the found records to eliminate the possibility of duplicate returns
 * @todo Add an array of protected Record types that users without a certain ACL privilege will not be able to search (implies ACL integration)
 * @package Omeka
 **/
class Kea_Controller_Search_Lucene extends Kea_Controller_Search_Abstract
{

	/**
	 * Retrieve the current number of records in the Lucene index
	 *
	 * @return int
	 **/
	public function getTotal() {
		$index = new Zend_Search_Lucene(SEARCH_DIR);
		return $index->count();
	}
	
	public function run() {
		$index = new Zend_Search_Lucene(SEARCH_DIR);
		$hits = $index->find($this->terms);
		if(!empty($this->_targetClass)) return $this->targetResults($hits);
		else return $this->allResults($hits);

	}
	
	/**
	 * Searches for a specific record class or those that have related elements
	 * 
	 * @todo Make this method recursive to deal with selective search where $this->_targetClass is an array of record classes to search
	 * @return Doctrine_Collection
	 **/	
	protected function targetResults($hits) {
		
		$table = Doctrine_Manager::getInstance()->getTable($this->_targetClass);
		$records = new Doctrine_Collection($this->_targetClass);
		
		// I feel uncomfortable using this over and over, esp. because its a bit of a hack, but it is necessary in this case to check for relations 
		// that used pluralized aliases
		$pluralized = $this->_targetClass.'s';
		
		$targetClass = $this->_targetClass;
		
		$start = $this->offset;
		$end = $this->per_page + $start;
		
		foreach( $hits as $key => $hit )
		{
			//Only pull these hits if we are on the correct offset
			//Can't figure out if this is any faster than just searching with straight SQL, but it probably is
			if(!$start || ($key >= $start && $key <= $end)) {
				$id = $hit->getDocument()->id;
				$model = $hit->getDocument()->model_name;
				//If we find something but it isn't the target class, we need to check if it is related to the target class somehow
				if($model != $targetClass) {
					$foundRecord = Doctrine_Manager::getInstance()->getTable($model)->find($id);
					
					//one-to-one
					if($foundRecord->hasRelation($targetClass)) {
						$related = $foundRecord->$targetClass;
						if($related->exists())
							$records->add($foundRecord->$targetClass);
					//one-to-many	
					}elseif($foundRecord->hasRelation($pluralized)){
						$relatedRecords = $foundRecord->$pluralized;
						foreach( $relatedRecords as $key => $relatedRecord )
						{
							$records->add($relatedRecord);
						}
					}
				} else {
					$foundRecord = $table->find($id);
					$records->add($foundRecord);
				}				
			}
		}
		
		return $records;
	}
	
	/**
	 * Search all the records in the database
	 * This one is different from just searching a single record class b/c it uses an array instead of a Doctrine_Collection to hold the results
	 *
	 * @return array
	 **/
	protected function allResults($hits) {
		$records = array();
		
		//Duplicated in above method
		$start = $this->offset;
		$end = $this->per_page + $start;
		
		foreach( $hits as $key => $hit )
		{
			$doc = $hit->getDocument();
			//Duplicated above
			if(!$start || ($key >= $start && $key <= $end)) {
				$table = Doctrine_Manager::getInstance()->getTable($doc->model_name);
				
				//This result set uses the search hit key as the key for results
				$records[$key] = $table->find($doc->id);
			}
		}
		
		return $records;
	}
		
} // END class Kea_Controller_Search

?>