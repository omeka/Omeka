<?php
/**
 * Kea_Record 
 *
 * Customized wrapper for Doctrine_Record
 *
 * @package Kea
 * 
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
	protected $constraints = array();
	
	public function setUp() 
	{
		$bound = Kea_Controller_Plugin_Broker::getInstance()->getBound(get_class($this));
		foreach ($bound as $key => $bind) {
			$method = $bind[0];
			$component = $bind[1];
			$relation = $bind[2];
			$this->$method($component, $relation);
		}
	}
	
	/**
	 * Retrieve the error message associated with a specific field if it exists, or retrieve all errors as a string
	 *
	 * @param string Name of the field (optional - if not given, all error messages are returned concatenated)
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
					$msg .= $field.": ".$error.".";
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
	
	/**
	 * Set all the record's values and relations given a multidimensional array
	 *
	 * @todo relational id fields should not have 0 as a value
	 * @return void
	 **/
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
	
	public function strip($text) {
		$text = get_magic_quotes_gpc() ? stripslashes( $text ) : $text;
		return $text;
	}
	
	public function setFromForm($array) {
		
		//Debatable as to whether this needs to go in the setArray() method or here - my guess is here
		foreach ($this->constraints as $constraint) {
			if(array_key_exists($constraint,$array) && empty($array[$constraint])) {
				$array[$constraint] = null;
			}
		}
		
		//Avoid security holes
		unset($array['id']);
		
		return $this->setArray($array, array($this, 'strip'));
	}
	
	/**
	 * toJson is an attempt to provide a relatively
	 * simple implementation for converting Doctrine_Record
	 * objects into JSON objects
	 * 
	 * This uses Zend_Json which does not seem to be able
	 * to handle syntax like Item.id = '1'. Instead the entire
	 * object needs to be represented by a hash like
	 * {"class":"item", "id":"1"}
	 * 
	 * 
	 */
	public function toJson()
	{
		require_once 'Zend/Json.php';
		$data = $this->getData();
		$data['id'] = $this->id;
		$data['class'] = ucfirst(strtolower(get_class($this)));
		return Zend_Json::encode($data);
	}
	
	/**
	 * Take in a json string, instantiate a model from it
	 * and assert that it is the same as the model in the
	 * database
	 */
	public function fromJson()
	{
		throw new Exception('This function not yet implemented');
	}
	
	/**
	 * This is a onvenience method to execute SQL statements directly
	 * within the record.  
	 *
	 * @return mixed
	 **/
	public function execute($sql, $params=array())
	{
		$res = $this->getTable()->getConnection()->execute($sql,$params);
		return $res->fetch();
	}
	
	/**
	 * Convenience method to execute DQL statements directly within the record
	 *
	 * @return mixed
	 **/
	public function executeDql($dql, $params=array(), $returnOne = false)
	{
		
	}
	
} // END abstract class Kea_Record
?>