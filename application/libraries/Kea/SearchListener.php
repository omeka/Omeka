<?php
/**
 * This will help us index things with the Zend_Search
 * @todo Must be able to index data types that aren't Items
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
			$ini = new Zend_Config_Ini(LIB_DIR.'/Kea/fields.ini');
		
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
					if($relation == $class) {
						$aggregate .= ' '.$record->$field;
					} elseif($record->$relation instanceof Doctrine_Collection) {
						foreach ($record->$relation as $rel) {
							$aggregate .= ' '.$rel->$field;
						}
					} elseif($record->hasRelation($relation)) {
						$aggregate .= ' '.$record->$relation->$field;
					}
				}
			}
			
			//Make a SQL statement that inserts it all into the fake table
			
			//@todo make sure aggregate is escaped correctly
			$sql = "INSERT INTO {$tableName}_fulltext (item_id, text) VALUES ({$record->id}, '".mysql_real_escape_string($aggregate)."');";
			Doctrine_Manager::connection()->execute($sql); 
		} else {
			if($record->hasRelation('Items')) {
				$items = $record->Items; 
			}elseif($record->hasRelation('Item')) {
	//			$items = new Doctrine_Collection('Item');
	//			$items->add($record->Item);
				$items = $record->Item;
			}
			if(isset($items)) {
				if($items instanceof Doctrine_Collection_Batch) {
					foreach ($items as $key => $item) {
						$this->onUpdate($item);
					}					
				}else {
					$this->onUpdate($items);
				}

			}
		}
	}
	public function onPreDelete(Doctrine_Record $record) {
		$class = get_class($record);
		if($class == 'Item') {
			$sql = "DELETE FROM items_fulltext WHERE item_id = {$record->id}";
			Doctrine_Manager::connection()->execute($sql); 
		}else {
			if($record->hasRelation('Items')) {
				foreach ($record->Items as $key => $item) {
					$this->onUpdate($item);
				}
			}elseif($record->hasRelation('Item')) {
				$this->onUpdate($record->Item);
			}
		}
	}
	
	public function onUpdate(Doctrine_Record $record) {
		$this->onPreDelete($record);
		$this->onInsert($record);
	}

} // END class SearchListener extends 

?>