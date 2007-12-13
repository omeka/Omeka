<?php 
/**
* Replacement for Doctrine_Record class
*/
class Omeka_Record implements ArrayAccess
{
	//Every table has a unique ID field
	public $id;
	
	//Current validation errors	
	protected $_errors = array();
		
	//Cache related elements so that we don't end up making extra SQL calls if we access them twice
	protected $_cache = array();
	
	//Ex. Taggable, Relatable, any object that acts as a mixin for Omeka_Record
	protected $_modules = array();
	
	/**
	 * Would be declared like thus:  
	 *
	 * array $_related = array('Sections'=>'loadSections'), where key is a cacheable property and value is a callback to obtain it
	 *  This will be used by self::__get() to automatically obtain/cache necessary elements
	 *	Meanwhile, there is no need for retrieved data to be saveable, as form data is handled through a different mechanism entirely
	 * @return void
	 **/
	protected $_related = array();
	
	private $_locked = false;
	
	public function __construct()
	{
		$this->construct();
	}
	
	protected function construct() {}
	
	//Delegate to the $_related callbacks for data retrieval (also have caching)
	public function __get($prop)
	{
		$data = null;
		$args = array();
		
		//Check the cache for data that has already been pulled
		if( !($data = $this->getCached($prop)) ) {

			//Check for a method that can pull the data
			if(array_key_exists($prop, $this->_related)) {
				$method = $this->_related[$prop];
				
				//If the method is an array, then the first arg is the callback and subsequent are the arguments
				if(is_array($method)) {
					$args = $method;
					$method = array_shift($args);
				}
				$data = call_user_func_array(array($this, $method), $args);
				
				$this->addToCache($data, $prop);
			}
		}

		return $data;
	}
	
	//Delegate to the $_modules for mixin-like behavior
	public function __call($m, $a)
	{
		return $this->delegateToModules($m, $a);
	}
	
	/**
	 * Instances of Omeka_Record_Modules (which are mostly plugins though they mix behavior)
	 *
	 * @return void
	 **/
	protected function delegateToModules($method, $args=array(), $all=false)
	{
		foreach ($this->_modules as $k => $module) {
			if(method_exists($module, $method)) {
				$called = true;
				$res = call_user_func_array(array($module, $method), $args);
				if(!$all) return $res;
			}
		}
		if(count($this->_modules) and !$called) {
			throw new Exception( "Method named $method does not exist!"  );
		}		
	}
	
	/**
	 * Maybe this is an error in design, but right now there are 3 different types of callbacks
	 * within Omeka_Record:
	 *		Omeka_Record hooks like Omeka_Record::afterDelete()
	 *		Record module hooks like Taggable::afterSave()
	 *		Plugin hooks like 'before_delete_item'
	 * 
	 * This function handles that stack in the proper order while reducing the duplication of code
	 *
	 * @param $event string camelCased name for the event i.e. beforeDelete
	 * @return void
	 **/
	protected function runCallbacks($event)
	{
		//All arguments beyond the first are optional, and are sent to the various callbacks
		$args = func_get_args();
		array_shift($args);	
		
		//Callback from within the record
		call_user_func_array(array($this, $event), $args);

		//Module callbacks
		$this->delegateToModules($event, $args, true);
		
		
		//Format the name of the plugin hook so it's in all lowercase with underscores
		//Taken from Doctrine::tableize()
		$plugin_hook = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $event));
		
		$plugin_hook .= '_' . strtolower(get_class($this));
		
		//Plugins called from within the record always receive that record instance as the first argument
		array_unshift($args, $this);
				
		//This is duplicated from the fire_plugin_hook() function
		call_user_func_array(array(get_plugin_broker(), $plugin_hook), $args);
	}
	
	protected function firePlugin($event)
	{
		$hook = $event . '_' . strtolower(get_class($this));
		
		fire_plugin_hook($hook, $this);
	}
	
	protected function addToCache($obj, $name)
	{
		$this->_cache[$name] = $obj;
	}
	
	protected function getCached($name)
	{
		return $this->_cache[$name];
	}
	
	//In our simplified database models, the only necessary check is whether or not the ID is already set
	public function exists() {return (bool) !empty($this->id);}
		
	/**
	 * This should wrap around a _validate() method and add hooks to call the plugins
	 *
	 * @return void
	 **/
	protected function validate() {
		$this->runCallbacks('beforeValidate');
		
		$validator = $this->_validate();
		
		//If the custom validator returned an instance of Zend_Filter_Input, then mine it for broken shit
/*
		
		if($validator and ($validator instanceof Zend_Filter_Input)) {
			if($validator->hasMissing() or $validator->hasInvalid()) {
			
				$msgs = $validator->getMessages();
			
				foreach ($msgs as $field => $msg) {
					$this->addError($field, join(', ', $msg));
				}
			}
		}
*/	
		
		$this->runCallbacks('afterValidate');
	}
	
	//Template method for validation
	protected function _validate() {}
	
	public function isValid()
	{
		$this->validate();
		
		return !$this->hasErrors();	
	}
	
	public function getErrors()
	{
		return $this->_errors;
	}

	protected function hasErrors()
	{
		return count($this->getErrors());
	}
	
	protected function addError($field, $msg)
	{
		if($field == null) {
			$this->_errors[] = $msg;
		}else {
			
			//Only keep the first error that gets added, b/c subsequent 
			//errors may be directly related or otherwise redundant
			
			if(!array_key_exists($field, $this->_errors)) {
				$this->_errors[$field] = $msg;
			}			
		}
	}
	
	/**
	 * This function makes it so that the record cannot be saved 
	 * (useful after manipulating the record for strictly display purposes)
	 *
	 **/
	final public function lock()
	{
		$this->_locked = true;
	}

	//Retrieve the table class associated with the object
	public function getTable($class = null)
	{
		if(!$class) $class = get_class($this);
		
		return get_db()->getTable($class);
	}

	/**
	 * Get an array of all the fields and their values
	 *
	 * @return void
	 **/
	public function toArray()
	{
		$columns = $this->getTable()->getColumns();
		
		$fields = array();
		
		foreach ($columns as $col) {
			$fields[$col] = $this->$col;
		}	
		
		return $fields;
	}	
	
	/**
	 * Validate the record, then insert into the DB
	 *
	 * @throws Omeka_Db_Exception
	 * @return bool
	 **/
	public function save()
	{	
		if($this->_locked) {
			throw new Exception( 'Cannot save a locked record, man!' );
		}
			
		if(!$this->isValid()) return false;
		
		$was_inserted = !$this->exists();
		
		//Some callbacks
		if($was_inserted) {
			$this->runCallbacks('beforeInsert');
		}else {
			$this->runCallbacks('beforeUpdate');
		}
		
		$this->runCallbacks('beforeSave');
		
		//Only try to save columns in the $data that are actually defined columns for the model
		$data_to_save = $this->toArray();
	
		$insert_id = get_db()->insert(get_class($this), $data_to_save);
		
		if(is_numeric($insert_id)) {
			$this->id = $insert_id;
		}

		if($was_inserted) {
			//Run the local afterInsert hook, the modules afterInsert hook, then the plugins' insert_record hook
			$this->runCallbacks('afterInsert');
		}
		else {
			$this->runCallbacks('afterUpdate');
		}
		
		$this->runCallbacks('afterSave');
		
		return true;
	}
	
	public function forceSave()
	{
		if(!$this->isValid() or !$this->save()) {
			throw new Omeka_Validator_Exception($this->getErrors());
		}
		
		return true;
	}
	
	public function __clone()
	{
		$this->id = null;
	}
	
	/**
	 * Subclasses are actually responsible for running the SQL queries that do the deleting
	 *
	 * @return void
	 **/
	public function delete()
	{
		if($this->_locked) {
			throw new Exception( 'Cannot delete a locked record!' );
		}
		
		if(!$this->exists()) return false;
		
		//Check to see if the subclass delete() method exists
		
		$this->runCallbacks('beforeDelete');
		
		//Delete has an extra template method that is separate from the callbacks 
		//This is because the callbacks execute prior to actually deleting anything
		//So the state of the record must be maintained until all callbacks are done
		//Then _delete() template method takes over and all bets are off
		$this->_delete();
		
		//The main delete query
		$table = $this->getTable()->getTableName();
		
		$query = "DELETE FROM $table WHERE {$table}.id = ? LIMIT 1";
		get_db()->exec($query, array((int) $this->id));
		
		$this->id = null;
		$this->runCallbacks('afterDelete');
	}
	
	/**
	 * Here is where the record should take of deleting all its nonsense
	 *
	 * @return void
	 **/
	protected function _delete() {}
	
	//Basic Callbacks
	protected function beforeInsert() {}
	
	protected function afterInsert() {}

	protected function beforeSave() {}
	
	protected function afterSave() {}
	
	protected function beforeUpdate() {}
	
	protected function afterUpdate() {}
	
	protected function beforeDelete() {}
	
	protected function afterDelete() {}

	protected function beforeValidate() {}
	
	protected function afterValidate() {}

	//Setter methods 
	public function setArray($data)
	{
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
	}
	
	//Implementation of ArrayAccess
	
	public function offsetExists($name) {
		return isset($this->$name);
	}
	
	public function offsetUnset($name) {
		unset($this->$name);
	}

	public function offsetGet($name) {
		return $this->$name;
	}
	
	public function offsetSet($name, $value) {
		$this->$name = $value;
	}
	
	//Input-related


	/**
	 * Processes and saves the form to the given record
	 *
	 * @return boolean True on success, false otherwise
	 **/
	public function saveForm(&$post)
	{
	/*
		get_db()->beginTransaction();
	*/
		if(!empty($post))
		{					
			$clean = $this->filterInput($post);
			
			$clean = new ArrayObject($clean);
			
			$this->runCallbacks('beforeSaveForm', $clean);
			
			unset($clean['id']);
			
			$this->setArray($clean);
	
			try {
				//Save will return TRUE if there are no validation errors
				if($this->save()) {
					$this->runCallbacks('afterSaveForm', $clean);

					//	get_db()->commit();
					return true;
				}
				else {
					//	get_db()->rollback();
					$errors = $this->getErrors();
					throw new Omeka_Validator_Exception( $errors );
				}
			} catch (Omeka_Db_Exception $e) {
				header("HTTP/1.0 404 Not Found");
				Zend_Debug::dump( $e );exit;
			}
			
		}
		return false;
	}
	
	//Form callbacks
	protected function filterInput($post) { return $post; }
	protected function beforeSaveForm(&$post) {return true; }
	protected function afterSaveForm(&$post) {}
	
	/**
	 * Adapted from Doctrine_Validator_Unique
	 *
	 * Check to see whether a value for a specific field is allowed
	 *
	 * @return bool
	 **/
	protected function fieldIsUnique($field, $value=null)
	{
		$table = $this->getTable()->getTableName();
		$pk = 'id';

        $sql   = 'SELECT ' . $pk . ' FROM ' . $table . ' WHERE ' . $field . ' = ?';
        
        $values = array();
		
		if(!$value) {
			$value = $this->$field;
		}

        $values[] = $value;
        
        // If the record is not new we need to add primary key checks because its ok if the 
        // unique value already exists in the database IF the record in the database is the same
        // as the one that is validated here.
        if ($this->exists()) {
           $sql .= " AND {$pk} != ?";
           $values[] = $this->$pk;
        }
        
        $res = get_db()->query($sql, $values);

        return ( ! is_array($res->fetch()));
	}
	
	//Legacy methods (deprecate and remove these)

	/**
	 * This function is kind of a hack, used instead of inflection because that would be slower
	 *
	 * @return string
	 **/
	public function getPluralized($lower=true)
	{
		$pluralName = !empty($this->_pluralized) ? strtolower($this->_pluralized) : get_class($this) .'s';
		return ($lower) ? strtolower($pluralName) : $pluralName;
	}

	/**
	 * @move this to the ACL
	 *
	 * @return void
	 **/
	protected function userHasPermission($rule) {
		$resource = $this->getPluralized(false);				
		$acl = get_acl();
		return $acl->checkUserPermission($resource, $rule);
	}
}
 
?>