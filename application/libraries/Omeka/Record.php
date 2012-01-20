<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * A base class for domain objects, inspired by, though not strictly adherent to,
 * the ActiveRecord pattern.
 * 
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
abstract class Omeka_Record implements ArrayAccess
{
    /**
     * Zend_Date format for saving to the database.
     */
    const DATE_FORMAT = 'yyyy-MM-dd HH:mm:ss';
    
    /**
     * Unique ID for the record.
     *
     * All implementations of Omeka_Record must have a table containing an 'id'
     * column, preferably as the primary key.
     * @var integer
     */
    public $id;
    
    /**
     * Any errors raised during the validation process.
     *
     * @var Omeka_Validator_Errors 
     */
    private $_errors = array();
        
    /**
     * An in-memory cache for related objects that have been retrieved
     * via the magic __get() syntax.
     *
     * @var array
     * @see Omeka_Record::__get()
     * @see Omeka_Record::_addToCache()
     */
    protected $_cache = array();
    
    /**
     * Set of Omeka_Record_Mixin objects that are designed to extend
     * the behavior of Omeka_Record implementations.
     *
     * Examples include {@link Taggable}, {@link Relatable},
     * {@link ActsAsElementText}, etc.
     *
     * @see Omeka_Record_Mixin
     * @var array
     */
    protected $_mixins = array();
    
    /**
     * @internal The database object should be protected so it doesn't show up
     * when the object is serialized into JSON.
     * @var Omeka_Db
     */
    protected $_db;
    
    /**
     * Key/value pairs indicating aliases for methods that retrieve
     * related data objects.
     * For example, a subclass might define the following:
     * <code>
     *     protected $_related = array('Sections'=>'loadSections');
     * </code>
     * This would allow the client to write code like:
     * <code>
     *     $sections = $subclassInstance->Sections;
     * </code> 
     * Which would be equivalent to:
     * <code>
     *     $sections = $subclassInstance->loadSections();
     * </code>
     * The difference being, the former is cached so as to avoid multiple
     * trips to the database.
     * @var array
     * @see Omeka_Record::__get()
     */
    protected $_related = array();
    
    /**
     * Whether or not the record is locked.
     * Locked records cannot be saved.
     *
     * @var boolean
     */
    private $_locked = false;
    
    /**
     * List of built in callback methods.
     *
     * @var array
     */
    private $_eventCallbacks = array(
        'beforeValidate',
        'afterValidate',
        'beforeInsert',
        'beforeUpdate',
        'beforeSave',
        'afterInsert',
        'afterUpdate',
        'afterSave',
        'beforeDelete',
        'afterDelete',
        'beforeSaveForm',
        'afterSaveForm'
    );
    
    private $_pluginBroker;
    
    /**
     * @param Omeka_Db|null $db (optional) Defaults to the Omeka_Db instance from 
     * Omeka_Context.
     */
    public function __construct($db = null)
    {
        //Dependency injection, for testing
        if (!$db) {
            $db = Omeka_Context::getInstance()->getDb();
            if (!$db) {
                throw new Omeka_Record_Exception("Unable to retrieve database instance from Omeka_Context.");
            }
        }
        
        $this->_db = $db;
        
        $this->_errors = new Omeka_Validator_Errors;
        $this->_initializeMixins();
        $this->construct();
    }
    
    /**
     * Subclass constructor behavior.
     *
     * Subclasses of Omeka_Record can override this function to add behavior to
     * the constructor without overriding __construct.
     *
     * @todo Should __construct() be declared final if this the preferred method?
     * @return void
     */
    protected function construct() {}
    
    /**
     * Unsets mixins, which contain circular references, upon record destruction
     * 
     * IMPORTANT: Solves a memory leak when retrieving/saving records.
     * 
     * Required because PHP 5.2 does not do garbage collection on circular references.
     */
    public function __destruct()
    {
         unset($this->_mixins);
    }
    
    /**
     * Retrieve database records that are associated with the current one.
     *
     * @see Omeka_Record::$_related
     * @uses Omeka_Record::_addToCache()
     * @uses Omeka_Record::_getCached()
     * @param string $prop Related data to retrieve.
     * @return mixed
     */
    public function __get($prop)
    {
        $data = null;
        $args = array();
        
        // Check the cache for data that has already been pulled
        if (!($data = $this->_getCached($prop))) {
            
            // Check for a method that can pull the data
            if (array_key_exists($prop, $this->_related)) {
                $method = $this->_related[$prop];
                
                // If the method is an array, then the first arg is the callback 
                // and subsequent are the arguments
                if (is_array($method)) {
                    $args = $method;
                    $method = array_shift($args);
                }
                $data = call_user_func_array(array($this, $method), $args);
                
                $this->_addToCache($data, $prop);
            }
        }
        
        return $data;
    }
    
    /**
     * Delegate unknown method calls to Omeka_Record_Mixin instances.
     * 
     * @see Omeka_Record::delegateToMixins()
     * @param string $m Method name.
     * @param array $a Method arguments.
     * @return mixed
     */
    public function __call($m, $a)
    {
        return $this->delegateToMixins($m, $a);
    }
    
    /**
     * Initialize the mixins for a record. 
     * 
     * Any Omeka_Record subclass that uses mixins should initialize them here, 
     * since this is called on construction and when mixins need to be 
     * reinitialized.
     */
    protected function _initializeMixins() {}
    
    /**
     * Delegate to the given method in one or more mixin instances.
     * 
     * @param string $method
     * @param array $args
     * @param boolean $all (optional) Whether or not to call the same method on
     * every mixin instance that has that method.  Defaults to false.
     * @return mixed If $all is false, the return value from the invoked method.  
     * Otherwise there is no return value.
     */
    protected function delegateToMixins($method, $args = array(), $all = false)
    {
        $methodFound = false;
        
        if (!$this->_mixins) {
            $this->_mixins = array();
            $this->_initializeMixins();
        }
        
        if (!count($this->_mixins)) {
            // The event callbacks, e.g. beforeValidate() are common to all
            // mixins.  If attempting to trigger one of these callbacks on an
            // empty mixin list, we 'found' the method.
            $methodFound = in_array($method, $this->_eventCallbacks);
        }
        
        foreach ($this->_mixins as $k => $mixin) {
            if (method_exists($mixin, $method)) {
                $methodFound = true;
                $res = call_user_func_array(array($mixin, $method), $args);
                if (!$all) {
                    return $res;
                }
            }
        }
        if (!$methodFound) {
            throw new BadMethodCallException( "Method named $method() does not exist."  );
        }
    }
    
    /**
     * Invoke all callbacks associated with a specific record event.
     * 
     * Callbacks execute in the following order:
     *  - Omeka_Record hooks like Omeka_Record::afterDelete()
     *  - Record mixin hooks like Taggable::afterSave()
     *  - Generic record plugin hooks like 'before_delete_record'
     *  - Specific record plugin hooks like 'before_delete_item'
     * 
     * @param $event string camelCased name for the event i.e. beforeDelete
     * @return void
     */
    protected function runCallbacks($event)
    {
        
        // All arguments beyond the first are optional, and are sent to the 
        // various callbacks
        $args = func_get_args();
        array_shift($args);

        // Callback from within the record
        call_user_func_array(array($this, $event), $args);
         
        // Module callbacks
        $this->delegateToMixins($event, $args, true);
             
        // Format the name of the plugin hook so it's in all lowercase with 
        // underscores. Taken from Doctrine::tableize()
        $plugin_hook_base = Inflector::underscore($event);
        $plugin_hook_general = $plugin_hook_base . '_record'; 
        $plugin_hook_specific = $plugin_hook_base . '_' . Inflector::underscore(get_class($this));
        
        // Plugins called from within the record always receive that record 
        // instance as the first argument
        array_unshift($args, $this);
                    
        if ($broker = $this->getPluginBroker()) {
            // run a general hook (one which is not specific to the classs of the record)
            // this is used by plugins which may need to process every record, and 
            // cannot anticipate all of the class names of those records
            call_user_func_array(array($broker, $plugin_hook_general), $args);

            // run a hook specific to the class
            call_user_func_array(array($broker, $plugin_hook_specific), $args);
        }
    }
    
    /**
     * Fire a plugin hook associated with the given event.
     * 
     * @deprecated
     * @see Omeka_Record::runCallbacks()
     * @param string $event Underscore-separated event name.
     * @return void
     */
    protected function firePlugin($event)
    {
        $hook = $event . '_' . strtolower(get_class($this));
        fire_plugin_hook($hook, $this);
    }
    
    /**
     * Add a value to the record-specific cache.
     * 
     * @param mixed $value
     * @param string $key
     * @return void
     */
    private function _addToCache($value, $key)
    {
        $this->_cache[$key] = $value;
    }
    
    /**
     * Get a value from the record-specific cache.
     *
     * @param string $name
     * @return mixed 
     */
    private function _getCached($name)
    {
        if (isset($this->_cache[$name])) {
            return $this->_cache[$name];
        }
    }
    
    /**
     * Determine whether or not this record is persistent in the database.  
     * 
     * For simplicity, non-persistent records are indicated by the lack of a
     * value for the 'id' column.
     *
     * @return boolean
     */
    public function exists()
    {
        return is_numeric($this->id) && !empty($this->id);
    }
    
    /**
     * Validate the record.
     * 
     * Validation should be handled by the _validate() method.
     * 
     * @uses Omeka_Record::_validate()
     * @return void
     */
    protected function validate() 
    {
        $this->runCallbacks('beforeValidate');
        $validator = $this->_validate();        
        $this->runCallbacks('afterValidate');
    }
    
    /**
     * Template method for defining record validation rules.
     * 
     * Should be overridden by subclasses.
     * 
     * @return void
     */
    protected function _validate() {}
    
    /**
     * Determine whether or not the record is valid.
     * 
     * @uses Omeka_Record::validate()
     * @uses Omeka_Record::hasErrors()
     * @return boolean
     */
    public function isValid()
    {
        $this->validate();
        return !$this->hasErrors();    
    }
    
    /**
     * Retrieve validation errors associated with this record.
     * 
     * @return Omeka_Validator_Errors
     */
    public function getErrors()
    {
        return $this->_errors;
    }
    
    /**
     * Determine whether or not this record has any validation errors.
     * 
     * @return boolean
     */
    public function hasErrors()
    {
        return (bool) count($this->getErrors());
    }
    
    /**
     * Add a validation error for a specific field.
     * 
     * Currently limited to a single error per field, so multiple error messages
     * must be concatenated together.
     * 
     * @param string|null $field Name of the field.  This can be null to indicate
     * a general error not associated with a specific field.
     * @param string $msg The error message.
     * @return void
     */
    public function addError($field, $msg)
    {
        if ($field == null) {
            $this->_errors[] = $msg;
        } else {
            // Only keep the first error that gets added, b/c subsequent 
            // errors may be directly related or otherwise redundant
            if (!array_key_exists($field, $this->_errors)) {
                $this->_errors[$field] = $msg;
            }            
        }
    }
    
    /**
     * Combine errors from a different Omeka_Record instance with the errors
     * already on this record.
     *
     * @see Item::_validateElements()
     * @param Omeka_Record $record
     * @return void
     */
    public function addErrorsFrom(Omeka_Record $record)
    {
        $errors = $record->getErrors();
        foreach ($errors->get() as $field => $error) {
            $this->addError($field, $error);
        }
    }
    
    /**
     * Prevent a record from being modified.
     * 
     * Can be used to prevent accidentally saving/deleting a record if its state may 
     * change but saving would be undesirable, such as modifying a record for
     * display purposes.
     *
     * @return void
     */
    final public function lock()
    {
        $this->_locked = true;
    }

    /**
     * Retrieve the Omeka_Db_Table instance associated with this record, or 
     * with that of any given record class.
     * 
     * @uses Omeka_Db::getTable()
     * @return Omeka_Db_Table
     */
    public function getTable($class = null)
    {
        if (!$class) {
            $class = get_class($this);
        }
        
        return $this->getDb()->getTable($class);
    }
    
    /**
     * Retrieve the Omeka_Db instance associated with this record.
     * 
     * @return Omeka_Db
     */
    public function getDb()
    {
        return $this->_db;
    }
    
    /**
     * Retrieve an associative array of all the record's columns and their 
     * values.
     * 
     * @uses Omeka_Db_Table::getColumns()
     * @return array
     */
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
     * Save the record.
     * 
     * If the record does not validate, nothing will happen.  If the record is 
     * not yet persisted, it will be inserted into the database.  If it is, then
     * the existing database row(s) will be updated.
     * 
     * @return boolean Whether or not the save was successful.
     */
    public function save()
    {    
        if ($this->_locked) {
            throw new Omeka_Record_Exception('Cannot save a locked record!');
        }
        
        if (!$this->isValid()) {
            return false;
        }
        
        $was_inserted = !$this->exists();
        
        // Some callbacks
        if ($was_inserted) {
            $this->runCallbacks('beforeInsert');
        } else {
            $this->runCallbacks('beforeUpdate');
        }
        
        $this->runCallbacks('beforeSave');
        
        // Only try to save columns in the $data that are actually defined
        // columns for the model
        $data_to_save = $this->toArray();
        
        $insert_id = $this->getDb()->insert(get_class($this), $data_to_save);
        
        if ($was_inserted && (empty($insert_id) || !is_numeric($insert_id))) {
            throw new Omeka_Record_Exception("LAST_INSERT_ID() did not return a numeric ID when saving the record.");
        }
        $this->id = $insert_id;
        
        if ($was_inserted) {
            // Run the local afterInsert hook, the modules afterInsert hook, then
            // the plugins' insert_record hook
            $this->runCallbacks('afterInsert');
        } else {
            $this->runCallbacks('afterUpdate');
        }
        
        $this->runCallbacks('afterSave');
               
        return true;
    }
    
    /**
     * Force the record to save.
     * 
     * @throws Omeka_Validator_Exception If the record cannot be saved for some 
     * reason.
     * @return boolean True if the save was successful, an exception is thrown
     * otherwise.
     */
    public function forceSave()
    {
        if (!$this->isValid() || !$this->save()) {
            throw new Omeka_Validator_Exception($this->getErrors());
        }
        
        return true;
    }
    
    /**
     * Clone the record.
     * 
     * Unsets the ID so the cloned record can be saved on its own.
     */
    public function __clone()
    {
        $this->id = null;
    }
    
    /**
     * Delete the record.
     * 
     * @return void
     */
    public function delete()
    {
        if ($this->_locked) {
            throw new Omeka_Record_Exception( 'Cannot delete a locked record!' );
        }
        
        if (!$this->exists()) {
            return false;
        }
        
        $this->runCallbacks('beforeDelete');
                
        // Delete has an extra template method that is separate from the 
        // callbacks. This is because the callbacks execute prior to actually 
        // deleting anything. So the state of the record must be maintained 
        // until all callbacks are done. Then _delete() template method takes 
        // over and all bets are off
        $this->_delete();
             
        // The main delete query
        $table = $this->getTable()->getTableName();
        
        $query = "DELETE FROM $table WHERE {$table}.id = ? LIMIT 1";
        $this->getDb()->delete($table, 'id = '  . (int) $this->id);
        
        $this->runCallbacks('afterDelete');
        $this->id = null;
    }
    
    /**
     * Template method for defining record deletion logic.
     * 
     * Subclasses can override this method to define additional logic for deleting
     * records.  Note that this is different from both the beforeDelete() and
     * afterDelete() hooks in that it executes after beforeDelete(), but before
     * the record is actually deleted.
     * 
     * Common use cases include emulating cascading deletes with other 
     * database rows.
     * 
     * @return void
     */
    protected function _delete() {}
    
    /**#@+
     * Template callback.
     * 
     * @return void
     */
    
    /**
     * Executes before the record is inserted.
     */
    protected function beforeInsert() {}
    
    /**
     * Executes after the record is inserted.
     */
    protected function afterInsert() {}
    
    /**
     * Executes before the record is saved.
     */
    protected function beforeSave() {}
    
    /**
     * Executes after the record is inserted.
     */
    protected function afterSave() {}
    
    /**
     * Executes before the record is updated.
     */
    protected function beforeUpdate() {}
    
    /**
     * Executes after the record is updated.
     */
    protected function afterUpdate() {}
    
    /**
     * Executes before the record is deleted.
     */
    protected function beforeDelete() {}
    
    /**
     * Executes after the record is deleted.
     */
    protected function afterDelete() {}
    
    /**
     * Executes before the record is validated.
     */
    protected function beforeValidate() {}
    
    /**
     * Executes after the record is validated.
     */
    protected function afterValidate() {}
    
    /**#@-*/
    
    /**
     * Set values for the record using an associative array or iterator.
     * 
     * @param array|Traversable $data
     * @return void
     */
    public function setArray($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    
    public function getPluginBroker()
    {
        if (!$this->_pluginBroker) {
            $this->setPluginBroker();
        }
        return $this->_pluginBroker;
    }
    
    public function setPluginBroker($broker = null)
    {
        if (!$broker) {
            $broker = Omeka_Context::getInstance()->getPluginBroker();
        }
        $this->_pluginBroker = $broker;
    }
    
    /**
     * Determine whether or not the given field has a value associated with it.
     * 
     * Required by ArrayAccess.
     * 
     * @param string $name
     * @return boolean
     */    
    public function offsetExists($name) 
    {
        return isset($this->$name);
    }
    
    /**
     * Unset the given field.
     * 
     * Required by ArrayAccess.
     * 
     * @param string $name
     * @return void
     */
    public function offsetUnset($name) 
    {
        unset($this->$name);
    }
    
    /**
     * Retrieve the value of a given field.
     * 
     * Required by ArrayAccess.
     * 
     * @param string $name
     * @return mixed
     */
    public function offsetGet($name) 
    {
        return $this->$name;
    }
    
    /**
     * Set the value of a given field.
     * 
     * Required by ArrayAccess.
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function offsetSet($name, $value) 
    {
        $this->$name = $value;
    }
        
    /**
     * Modify and save the given record given an associative array.
     * 
     * Filters data from $post before attempting to save the record.
     *
     * @throws Omeka_Validator_Exception If the form does not validate.
     * @param array $post An associative array, typically a copy of $_POST.
     * @return boolean True if the form saved successfully, false if there was
     * no post data.  Throws an exception if form does not validate.
     */
    public function saveForm($post)
    {        
        if(!empty($post))
        {                    
            $clean = $this->filterInput($post);
            $clean = new ArrayObject($clean);
            $this->runCallbacks('beforeSaveForm', $clean);
            $clean = $this->setFromPost($clean);
            
            //Save will return TRUE if there are no validation errors
            if ($this->save()) {
                $this->runCallbacks('afterSaveForm', $clean);
                return true;
            } else {
                $errors = $this->getErrors();
                throw new Omeka_Validator_Exception($errors);
            }            
        }
        return false;
    }
    
    /**
     * Filter the form input according to some criteria.
     * 
     * Template method should be overridden by subclasses that wish to implement
     * some sort of filtering criteria.
     * 
     * @param array $post
     * @return array Filtered post data.
     */
    protected function filterInput($post) 
    {
        return $post;
    }
    
    /**
     * Template callback that executes before the form data is saved to the 
     * record.
     * 
     * @internal This receives an ArrayObject instance to make it easier for
     * plugins to modify these values without having to pass arrays by
     * reference.
     * @param ArrayObject $post Subclasses may override this method in order to
     * modify the contents of $post before it is saved to the record.
     * @return void
     */
    protected function beforeSaveForm($post) 
    {
        return true;
    }
    
    /**
     * Set the record values from POST data.
     *
     * @param array $post
     * @return array POST data that has been filtered yet again.
     */
    protected function setFromPost($post) 
    {
        if (array_key_exists('id', $post)) {
            unset($post['id']);
        }
        $this->setArray($post);
        return $post;
    }
    
    /**
     * Template callback that executes after the form data has been saved to the
     * record.
     * 
     * @see Omeka_Record::beforeSaveForm()
     * @param ArrayObject $post
     * @return void
     */
    protected function afterSaveForm($post) 
    {
        return true;
    }
    
    /**
     * Check uniqueness of one of the record's fields.
     * 
     * @uses Zend_Validate_Db_NoRecordExists
     * @param string $field
     * @param mixed $value Optional If null, this will check the value of the
     * record's $field.  Otherwise check the uniqueness of this value for the
     * given field.
     * @return boolean
     */
    protected function fieldIsUnique($field, $value = null)
    {
        $value = $value ? $value : $this->$field;

        if ($value === null) {
            throw new Omeka_Record_Exception("Cannot check uniqueness of NULL value.");
        }

        $validatorOptions = array(
            'table'   => $this->getTable()->getTableName(),
            'field'   => $field,
            'adapter' => $this->getDb()->getAdapter()
        );

        // If this record already exists, exclude it from the validation
        if ($this->exists()) {
            $validatorOptions['exclude'] = array(
                'field' => 'id',
                'value' => $this->id
            );
        }

        $validator = new Zend_Validate_Db_NoRecordExists($validatorOptions);
        return $validator->isValid($value);
    }
        
    /**
     * Determine whether or not the ACL grants permission for the specific 
     * privilege.
     * 
     * Note that this is deprecated and may fail or throw exceptions if
     * the ACL definition does not include a resource with a name corresponding
     * to the pluralized form of this record's class name.
     * 
     * @deprecated
     * @param string $rule
     * @return boolean
     */
    protected function userHasPermission($rule) 
    {
        $resource = Inflector::pluralize(get_class($this));        
        if ($acl = Omeka_Context::getInstance()->getAcl()) {
            return $acl->checkUserPermission($resource, $rule);
        }
    }    
}
