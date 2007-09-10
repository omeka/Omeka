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
	
	protected $_strategies = array();
	
	protected $_pluralized;
	protected $_hidden;

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
					
					//Set empty form entries to the default value (or null if no default)
					//Otherwise force the form entry to be either a string or integer
					$default = $this->getTable()->getDefaultValueOf($key);
					
					if(empty($value)) {
						if($default !== null) {
							$value = $default;
						}else {
							$value = null;
						}
					}
					elseif($type == 'string' || $type == 'integer') {
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
		if($text !== null) {
			$text = get_magic_quotes_gpc() ? stripslashes( $text ) : $text;
		}
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
	
	public function getTableName($model=null)
	{
		if(!$model) {
			return $this->getTable()->getTableName();
		}
		
		return Zend::Registry('doctrine')->getTable($model)->getTableName();
	}
	
	/**
	 * Merge two records and all of their relevant join records together
	 *
	 * @return bool
	 **/
	public function merge($record)
	{
		throw new Exception( 'This function must be implemented for each relevant sub-class' );
	}
	
	/**
	 * This prevents access to a given field (both get and set) for the duration of the object's existence
	 *
	 * This is really a workaround for the most obvious solution, which is to not draw in the data from the DB in the first place
	 * Unfortunately the ORM does not understand this, so this is the current solution.
	 **/
	public function hideField($field)
	{
		$this->_hidden[$field] = true;
	}
	
	protected function fieldIsHidden($field)
	{
		$hidden = $this->_hidden;
		$bool = ($hidden and isset($hidden[$field]));
		return $bool;
	}
	
	public function get($name)
	{
		//Return nothing if the field has been hidden
		if($this->fieldIsHidden($name)) {
			return false;
		}else {
			return parent::get($name);
		}
	}
	
	public function set($name, $value)
	{
		if($this->fieldIsHidden($name)) {
			return false;
		}else {
			return parent::set($name, $value);
		}
	}
	
	//END HIDDEN FIELD MODIFICATIONS
	
	public function getPluralized($lower=true)
	{
		$pluralName = !empty($this->_pluralized) ? strtolower($this->_pluralized) : get_class($this) .'s';
		return ($lower) ? strtolower($pluralName) : $pluralName;
	}
	
	public function __call($m, $a)
	{
		foreach ($this->_strategies as $k => $strat) {
			if(method_exists($strat, $m)) {
				return call_user_func_array(array($strat, $m), $a);
			}
		}
	}
	
	/**
	 * @example $record->hasStrategy('Relatable') --> bool (true)
	 *
	 * @return bool
	 **/
	public function hasStrategy($name)
	{
		foreach ($this->_strategies as $k => $strat) {
			if($name == get_class($strat)) return true;
		}
		
		return false;
	}
	
	/**
	 * This is a onvenience method to execute SQL statements directly
	 * within the record.  
	 *
	 * @return mixed
	 **/
	public function execute($sql, $params=array(), $fetchOne = false)
	{
		$res = $this->getTable()->getConnection()->execute($sql,$params);
		if($fetchOne)
			return $res->fetchColumn(0);
		else 
			return $res->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Convenience method to execute DQL statements directly within the record
	 *
	 * @return mixed
	 **/
	public function executeDql($dql, $params=array(), $returnOne = false)
	{
		$q = new Doctrine_Query;
		$res = $q->parseQuery($dql)->execute($params);
		return ($returnOne) ? $res->getFirst() : $res;
	}
	
	/**
	 * Check if a given field is unique
	 *
	 * @return bool
	 **/
	public function isUnique($field)
	{
		if(is_array($field)) {
			$where = join(' = ? AND ', $field) . ' = ?';
			foreach ($field as $f) {
				$values[] = $this->$f;
			}
		}else {
			$where = "$field = ?";
			$values = array($this->$field);
		}
		try {
			$sql = "SELECT e.id as id FROM ".$this->getTableName()." e WHERE $where";
			$id = $this->execute($sql, $values);
			return (!count($id) or ( (count($id) == 1) and ($id[0]['id'] == $this->id) ));
				
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * Processes and saves the form to the given record
	 *
	 * @param Kea_Record
	 * @return boolean True on success, false otherwise
	 **/
	public function commitForm(&$post, $save=true, $options=array())
	{
	/*
		$conn = $this->_table->getConnection();
		$conn->beginTransaction();
	*/
	
		if(!empty($post))
		{		
			$clean = $post;
			
			if($this->preCommitForm($clean, $options) === false) {
				return false;
			}
			
			unset($clean['id']);
			$this->setFromForm($clean);
			if($save) {
				try {
					$this->save();
					$this->postCommitForm($post, $options);
				//	$conn->commit();
					return true;
				}
				catch(Doctrine_Validator_Exception $e) {
					$this->gatherErrors($e);
					$this->onFormError($post, $options);
					throw new Exception( $this->getErrorMsg() );
				//	$conn->rollback();
					return false;
				}	
			}
			
		}
		return false;
	}
	
	/**
	 * This is used solely by the commitForm() methods
	 *
	 * @return void
	 **/
	protected function userHasPermission($rule) {
		$user = Kea::loggedIn();
		$resource = $this->getPluralized(false);
		
		/*	'default' permission level is hard-coded here, may change later */
		$role = !$user ? 'default' : $user->role;
		
		$acl = Zend::Registry( 'acl' );
		
		//If the resource has no rule that would indicate permissions are necessary, then we assume access is allowed
		if(!$acl->resourceHasRule($resource,$rule)){
			return TRUE;
		} 
		
		return $acl->isAllowed($role, $resource, $rule);
	}
	
	
	/**
	 * Wrap Doctrine's delete function with a plugin hook that fires whenever a record gets deleted
	 *
	 * @return void
	 **/
	public function delete()
	{
		//i.e. 'delete_item'
		$hook = 'delete_' . strtolower(get_class($this));
		fire_plugin_hook($hook, $this);
		
		return parent::delete();
	}
	
	public function postSave()
	{
		$hook = 'save_' . strtolower(get_class($this));
		fire_plugin_hook($hook, $this);
	}
	
	public function postInsert()
	{
		$hook = 'insert_' . strtolower(get_class($this));
		fire_plugin_hook($hook, $this);
	}
	
	public function postUpdate()
	{
		$hook = 'update_' . strtolower(get_class($this));
		fire_plugin_hook($hook, $this);
	}
	
	protected function preCommitForm(&$post, $options) {return true;}
	
	protected function postCommitForm($post, $options) {}
	
	protected function onFormError($post, $options) {}
} // END abstract class Kea_Record
?>