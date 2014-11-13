<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A base class for domain objects, inspired by, though not strictly adherent 
 * to, the ActiveRecord pattern.
 * 
 * @package Omeka\Record
 */
abstract class Omeka_Record_AbstractRecord implements ArrayAccess
{
    /**
     * Unique ID for the record.
     *
     * All implementations of Omeka_Record_AbstractRecord must have a table 
     * containing an 'id' column, preferably as the primary key.
     * @var integer
     */
    public $id;
    
    /**
     * Any errors raised during the validation process.
     *
     * @var Omeka_Validate_Errors 
     */
    private $_errors = array();
        
    /**
     * An in-memory cache for related objects that have been retrieved
     * via the magic __get() syntax.
     *
     * @var array
     * @see Omeka_Record_AbstractRecord::__get()
     * @see Omeka_Record_AbstractRecord::_addToCache()
     */
    protected $_cache = array();
    
    /**
     * Set of Omeka_Record_Mixin_AbstractMixin objects that are designed to 
     * extend the behavior of Omeka_Record_AbstractRecord implementations.
     *
     * Examples include {@link Taggable}, {@link Relatable},
     * {@link ActsAsElementText}, etc.
     *
     * @see Omeka_Record_Mixin_AbstractMixin
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
     * @see Omeka_Record_AbstractRecord::__get()
     */
    protected $_related = array();
    
    /**
     * Storage for the POST data when handling a form.
     * 
     * @see Omeka_Record_AbstractRecord::setPostData()
     * @see Omeka_Record_AbstractRecord::save()
     * @var ArrayObject
     */
    protected $_postData;
    
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
        'beforeSave',
        'afterSave',
        'beforeDelete',
        'afterDelete',
    );
    
    private $_pluginBroker;
    
    /**
     * @param Omeka_Db|null $db (optional) Defaults to the Omeka_Db instance from 
     * the bootstrap.
     */
    public function __construct($db = null)
    {
        //Dependency injection, for testing
        if (!$db) {
            try {
                $db = Zend_Registry::get('bootstrap')->getResource('Db');
            } catch (Zend_Exception $e) {
                // No bootstrap...
            }
            if (!$db) {
                throw new Omeka_Record_Exception("Unable to retrieve database instance.");
            }
        }
        
        $this->_db = $db;
        
        $this->_errors = new Omeka_Validate_Errors;
        $this->_initializeMixins();
        $this->construct();
    }
    
    /**
     * Subclass constructor behavior.
     *
     * Subclasses of Omeka_Record_AbstractRecord can override this function to 
     * add behavior to the constructor without overriding __construct.
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
     * @see Omeka_Record_AbstractRecord::$_related
     * @uses Omeka_Record_AbstractRecord::_addToCache()
     * @uses Omeka_Record_AbstractRecord::_getCached()
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
     * Delegate unknown method calls to Omeka_Record_Mixin_AbstractMixin 
     * instances.
     * 
     * @see Omeka_Record_AbstractRecord::delegateToMixins()
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
     * Any Omeka_Record_AbstractRecord subclass that uses mixins should 
     * initialize them here, since this is called on construction and when 
     * mixins need to be reinitialized.
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
            // The event callbacks are common to all mixins. If attempting to 
            // trigger one of these callbacks on an empty mixin list, we 'found' 
            // the method.
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
     *  - Omeka_Record_AbstractRecord hooks like Omeka_Record_AbstractRecord::afterDelete()
     *  - Record mixin hooks like Taggable::afterSave()
     *  - Generic record plugin hooks like 'before_delete_record'
     *  - Specific record plugin hooks like 'before_delete_item'
     * 
     * @param $event string camelCased name for the event i.e. beforeDelete
     * @param $args array optional array of arguments for the callback
     */
    protected function runCallbacks($event, $args = array())
    {
        // Callback from within the record
        call_user_func(array($this, $event), $args);
         
        // Module callbacks
        $this->delegateToMixins($event, array($args), true);
             
        // Format the name of the plugin hook so it's in all lowercase with 
        // underscores. Taken from Doctrine::tableize()
        $plugin_hook_base = Inflector::underscore($event);
        $plugin_hook_general = $plugin_hook_base . '_record'; 
        $plugin_hook_specific = $plugin_hook_base . '_' . Inflector::underscore(get_class($this));
        
        // Plugins called from within the record always receive that record.
        $args = array('record' => $this) + $args;
        
        if ($broker = $this->getPluginBroker()) {
            // run a general hook (one which is not specific to the classs of the record)
            // this is used by plugins which may need to process every record, and 
            // cannot anticipate all of the class names of those records
            $broker->callHook($plugin_hook_general, $args);

            // run a hook specific to the class
            $broker->callHook($plugin_hook_specific, $args);
        }
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
     * Get a property about the record for display purposes.
     *
     * @param string $property Property to get. Always lowercase.
     * @return mixed
     */
    public function getProperty($property)
    {
        switch ($property) {
            case 'has_tags':
                try {
                    return (bool) $this->getTags();
                } catch (BadMethodCallException $e) {
                    return false;
                }
                break;
            default:
                if (in_array($property, $this->getTable()->getColumns())) {
                    return $this->$property;
                }
                break;
        }
        throw new InvalidArgumentException(__("'%s' is an invalid special value.", $property));
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
     * @uses Omeka_Record_AbstractRecord::validate()
     * @uses Omeka_Record_AbstractRecord::hasErrors()
     * @return boolean
     */
    public function isValid()
    {
        $this->_validate();
        return !$this->hasErrors();    
    }
    
    /**
     * Retrieve validation errors associated with this record.
     * 
     * @return Omeka_Validate_Errors
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
     * Combine errors from a different Omeka_Record_AbstractRecord instance with 
     * the errors already on this record.
     *
     * @see Item::_validateElements()
     * @param Omeka_Record_AbstractRecord $record
     * @return void
     */
    public function addErrorsFrom(Omeka_Record_AbstractRecord $record)
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
            // Cast true to 1 and false to 0 to reflect what MySQL is expecting.
            $fields[$col] = is_bool($this->$col) ? (int) $this->$col : $this->$col;
        }
        return $fields;
    }    
    
    /**
     * Save the record.
     *
     * @throws Omeka_Validate_Exception
     * @throws Omeka_Record_Exception
     * @see Omeka_Record_AbstractRecord::setPostData()
     * @param boolean $throwIfInvalid
     * @return boolean Whether the save was successful.
     */
    public function save($throwIfInvalid = true)
    {
        if ($this->_locked) {
            throw new Omeka_Record_Exception('Cannot save a locked record!');
        }
        
        $wasInserted = !$this->exists();
        
        // Set the arguments for the before/afterSave callbacks.
        $callbackArgs = array('post' => false, 'insert' => false);
        if ($this->_postData) {
            $callbackArgs['post'] = $this->_postData;
        }
        if ($wasInserted) {
            $callbackArgs['insert'] = true;
        }
        
        $this->runCallbacks('beforeSave', $callbackArgs);
        
        if (!$this->isValid()) {
            if ($throwIfInvalid) {
                throw new Omeka_Validate_Exception($this->getErrors());
            } else {
                return false;
            }
        }
        
        // Save the record to the database. Only save data that are properties 
        // defined by the record.
        $insertId = $this->getDb()->insert(get_class($this), $this->toArray());
        
        if ($wasInserted && (empty($insertId) || !is_numeric($insertId))) {
            throw new Omeka_Record_Exception("LAST_INSERT_ID() did not return a numeric ID when saving the record.");
        }
        $this->id = $insertId;
        
        $this->runCallbacks('afterSave', $callbackArgs);
        
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
     * Executes before the record is saved.
     */
    protected function beforeSave($args) {}
    
    /**
     * Executes after the record is inserted.
     */
    protected function afterSave($args) {}
    
    /**
     * Executes before the record is deleted.
     */
    protected function beforeDelete() {}
    
    /**
     * Executes after the record is deleted.
     */
    protected function afterDelete() {}
    
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
            try {
                $broker = Zend_Registry::get('bootstrap')->getResource('PluginBroker');
            } catch (Zend_Exception $e) {
                $broker = null;
            }
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
     * Filter the form input according to some criteria.
     * 
     * Template method should be overridden by subclasses that wish to implement
     * some sort of filtering criteria.
     * 
     * @param array $post
     * @return array Filtered post data.
     */
    protected function filterPostData($post) 
    {
        return $post;
    }
    
    /**
     * Set the POST data to the record.
     * 
     * @see Omeka_Record_AbstractRecord::save()
     * @param array $post
     */
    public function setPostData($post)
    {
        $post = new ArrayObject($this->filterPostData($post));
        
        if (array_key_exists('id', $post)) {
            unset($post['id']);
        }
        
        $this->setArray($post);
        $this->_postData = $post;
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
     * Get the routing parameters or the URL string to this record.
     * 
     * The record_url() global uses this method to get routing parameters for 
     * non-standard records, e.g. records defined by plugins. Subclasses should 
     * override this method if the default route (as defined below) is 
     * incorrect.
     * 
     * @param string $action
     * @return string|array A URL string or a routing array.
     */
    public function getRecordUrl($action = 'show')
    {
        // Inflect the controller from the record type. This works primarily 
        // with built-in records that have controllers within the default 
        // module.
        $controller = str_replace('_', '-', Inflector::tableize(get_class($this)));
        
        // Return the default routing parameters. 
        return array('controller' => $controller, 'action' => $action, 'id' => $this->id);
    }

    /**
     * Get a representative file for this record.
     *
     * @return File|null
     */
    public function getFile()
    {
        return null;
    }
}
