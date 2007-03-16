<?php
/**
 * This will help us index things with the Zend_Search
 *
 * @todo optimization
 * @package Omeka
 * 
 **/
class Kea_SearchListener extends Doctrine_EventListener
{		
	public function onInsert(Doctrine_Record $record) {
		$class = get_class($record);
		if($class == 'Item') {
			//Get a list of the indexed elements from the ini file
			require_once 'Zend/Config/Ini.php';
			$ini = new Zend_Config_Ini(LIB_DIR.'/Kea/Controller/Search/fields.ini');
		
			$table = Doctrine_Manager::getInstance()->getTable($class);
			$tableName = $table->getTableName();
		
			//Cook up an aggregate text value of all the indexable values for this Item
			$aggregate = '';
		
			$relations = explode('|', $ini->fields->$class->relation);
			array_unshift($relations, $class);
			foreach($relations as $relation) {
				$match = $ini->fields->$relation->match;
				$fields = explode('|', $match);
				foreach ($fields as $field) {
					$plural = $relation.'s';
					if($relation == $class) {
						$aggregate .= ' '.$record->$field;
					} elseif($record->hasRelation($plural)) {
						//In this case it must be a plural relation
						
						foreach ($record->$plural as $key => $relatedRecord) {
							$aggregate .= ' '.$relatedRecord->$field;
						}
					}elseif($record->hasRelation($relation)) {
						$aggregate .= ' '.$record->$relation->$field;
					}
				}
			}
		
			//Make a SQL statement that inserts it all into the fake table
			
			//@todo make sure aggregate is escaped correctly
			$sql = "INSERT INTO {$tableName}_fulltext (id, text) VALUES ({$record->id}, '$aggregate');";
			Doctrine_Manager::connection()->execute($sql); 
		}

	}
	public function onPreDelete(Doctrine_Record $record) {
		$class = get_class($record);
		if($class == 'Item') {
			$tableName = Doctrine_Manager::getInstance()->getTable($class)->getTableName();
			$sql = "DELETE FROM {$tableName}_fulltext WHERE id = {$record->id}";
			Doctrine_Manager::connection()->execute($sql); 
		}
	}
	
	public function onUpdate(Doctrine_Record $record) {
		$this->onPreDelete($record);
		$this->onInsert($record);
	}

} // END class SearchListener extends 

?>