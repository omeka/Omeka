<?php
/**
 * Kea_Record 
 *
 * Customized wrapper for Doctrine_Record
 *
 * @package Kea
 * @author Kris Kelly
 **/
abstract class Kea_Record extends Doctrine_Record
{
	public function dump() {
		foreach( $this as $key => $value )
		{
			echo $key . '=' . $value . '<br />';
		}
	}
	
	public function dumpSave() {
		try {
			$this->save();
		}catch( Doctrine_Validator_Exception $e) {
			foreach( $e->getInvalidRecords() as $key => $record )
			{
				echo get_class( $record )."<br/>\n";
				foreach( $record->getErrorStack() as $name => $stack )
				{
					echo "$name) ".print_r($stack, true). "<br/>\n";
				}
			}
		}
	}
	
	public function setArray( $array, $callback = null ) {
		foreach( $array as $key => $value )
		{
			if($this->hasRelation($key)) {
				if(!is_array($value)) {
					$type = $this->getTable()->getTypeOf($key);
					if($type == 'string' || $type == 'integer') {
						settype($value, $type);
					}
				
					$this->$key = (!$callback) ? $value : call_user_func_array($callback, array($value) );
				}
				else {
					if($this->hasRelation($key)) {
					
						if($this->$key instanceof Doctrine_Collection) {
						
							foreach( $value as $index => $coll_values )
							{
								$rel = $this->$key;
								$rel[$index]->setArray($coll_values, $callback);
							}
						}
						//its an instance of Doctrine_Record
						else {
							$this->$key->setArray($value, $callback);
						}
					}
				}
			}
		}
		return $this;
	}
} // END abstract class Kea_Record
?>