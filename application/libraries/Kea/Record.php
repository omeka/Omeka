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
	/**
	 * Ex.
	 * $error_messages['title']['unique'] = "Title must be unique.  That title has already been used."
	 *
	 * @var array
	 **/
	protected $error_messages = array();
	
	
	/**
	 * 
	 * @todo Make this method recursive so that $this->getErrorMsg() will retrieve all error messages
	 * @return string
	 **/
	public function getErrorMsg($field = null) 
	{
		$stack = $this->getErrorStack();
		$msg = '';
		if(!$field) {
			foreach( $stack as $name => $errors )
			{
				$msg .= $this->getErrorMsg($name);
			}
		}
		if(isset($stack[$field])) {
			$errors = $stack[$field];
			foreach( $errors as $error )
			{
				if(isset($this->error_messages[$field][$error]))
				{
					$msg .= $this->error_messages[$field][$error];
				}else{
					$msg .= $error;
				}
			}
		}
		return $msg;
	}
	
	/**
	 * What I'm trying to do here is coalesce all the error messages into a single Record (usually the Item)
	 *
	 * @return void
	 **/
	public function gatherErrors(Doctrine_Validator_Exception $e)
	{
		$this_stack = $this->getErrorStack();
		$invalid = $e->getInvalidRecords();
		foreach( $invalid as $record )
		{
			if($record->id != $this->id) {
				$other_stack = $record->getErrorStack();
				foreach( $other_stack as $field => $errors )
				{
					$this_stack->add(get_class($record), $record->getErrorMsg());
				}
			}
		}
		$this->errorStack($this_stack);
	}
	
	public function dump() {
		foreach( $this as $key => $value )
		{
			echo $key . '=' . $value . '<br />';
		}
	}
	
	public function dumpSave($dumpValues = false) {
		try {
			$this->save();
		}catch( Doctrine_Validator_Exception $e) {
			foreach( $e->getInvalidRecords() as $key => $record )
			{
				echo get_class( $record ).(($record->exists()) ? " with id = ".$record->id : "")."<br/>\n";
				foreach( $record->getErrorStack() as $name => $stack )
				{
					echo "$name) ".print_r($stack, true). "<br/>\n";
				}
				
				if($dumpValues) $record->dump();
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
	
	private function strip($text) {
		$text = get_magic_quotes_gpc() ? stripslashes( $text ) : $text;
		return $text;
	}
	
	public function setFromForm($array) {
		return $this->setArray($array, array($this, 'strip'));
	}
	
} // END abstract class Kea_Record
?>