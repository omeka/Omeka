<?php
/**
 * Helper functions that are always available in Omeka.  As global functions,
 * these should be used as little as possible in the application code
 * to reduce coupling.
 *
 * @package Omeka
 **/
 
/**
 * Retrieve an option from the Omeka database.
 * 
 * If the returned value represents an object or array, it must be unserialized
 * by the caller before use.  For example, 
 * <code>$object = unserialize(get_option('plugin_object'))</code>.
 * 
 * @param string $name
 * @return string
 **/ 
function get_option($name) {
    $options = Omeka_Context::getInstance()->getOptions();
    return $options[$name];
}

/**
 * Set an option in the Omeka database.
 * 
 * Note that objects and arrays must be serialized before being saved.
 * 
 * @see get_option()
 * @param string $name
 * @param string $value
 * @return void
 **/
function set_option($name, $value)
{
    $db = get_db();
    $db->exec("REPLACE INTO $db->Option (name, value) VALUES (?, ?)", array($name, $value));
    
    //Now update the options hash so that any subsequent requests have it available
    $options = Omeka_Context::getInstance()->getOptions();
    $options[$name] = $value;
    
    Omeka_Context::getInstance()->setOptions($options);
}

/**
 * Delete an option from the database.  
 * 
 * @param string $name
 * @return void
 **/
function delete_option($name)
{
    $db = get_db();
    $sql = "
    DELETE 
    FROM $db->Option 
    WHERE `name` = ?";
    $db->query($sql, array($name));
    
    $options = Omeka_Context::getInstance()->getOptions();
    if (isset($options[$name])) {
        unset($options[$name]);
    }
    Omeka_Context::getInstance()->setOptions($options);
}

/**
 * Generate a URL slug from a piece of text.
 * 
 * Trims whitespace, replaces some prohibited characters with hyphens, and 
 * converts the resulting string to lowercase.
 * 
 * @param string $text
 * @return string
 **/
function generate_slug($text)
{
    $slug = trim($text);
    
    //Replace prohibited characters in the title with - 's
    $prohibited = array(':', '/', ' ', '.', '#');
    $replace = array_fill(0, count($prohibited), '-');
    $slug = str_replace($prohibited, $replace, strtolower($slug) );
    return $slug;
}

/**
 * Retrieve one column of a multidimensional array as an array.
 * 
 * @param string|integer $col
 * @param array
 * @return array
 **/
function pluck($col, $array)
{
    $res = array();
    foreach ($array as $k => $row) {
        $res[$k] = $row[$col];
    }
    return $res;    
} 

/**
 * Retrieve the User record associated with the currently logged in user.
 * 
 * @return User|null Null if no user is logged in.
 **/
function current_user()
{
    return Omeka_Context::getInstance()->getCurrentUser();
}

/**
 * Retrieve the database object.
 * 
 * @return Omeka_Db
 **/
function get_db()
{
    return Omeka_Context::getInstance()->getDb();
}

/**
 * Log a message with 'DEBUG' priority.
 * 
 * This will do nothing if logging is not enabled via config.ini's log.errors
 * setting.
 * 
 * @param string
 * @return void
 **/
function debug($msg)
{
    $context = Omeka_Context::getInstance();
    $logger = $context->getLogger();
    if ($logger) {
        $logger->debug($msg);
    }
}

/**
 * Called during startup to strip out slashes from the request superglobals in 
 * order to avoid problems with PHP's magic_quotes setting.
 * 
 * Does not need to be called elsewhere in the application.
 * 
 * @access private
 * @return mixed
 **/
function stripslashes_deep($value)
{
     $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);

     return $value;
}

/**
 * Declare a plugin hook implementation within a plugin.
 * 
 * @param string
 * @param mixed $callback Any valid PHP callback.
 * @return void
 **/
function add_plugin_hook($hook, $callback)
{
    get_plugin_broker()->addHook($hook, $callback);
} 

/**
 * Declare the point of execution for a specific plugin hook.  
 * 
 * All plugin implementations of a given hook will be executed when this is called.
 * 
 * The first argument corresponds to the string name of the hook.  Subsequent
 * arguments will be passed to the plugin hook implementations.
 * 
 * <code>fire_plugin_hook('after_save_item', $item, $arg2);  //would call the plugin hook 
 * 'after_save_item' with those 2 arguments.</code>
 *
 * @access private
 * @param string $hookName
 * @return array
 **/
function fire_plugin_hook()
{
    if ($pluginBroker = get_plugin_broker()) {
        $args = func_get_args();
        $hook = array_shift($args);
        return call_user_func_array(array($pluginBroker, $hook), $args);
    }
}

/**
 * Retrieve the output of fire_plugin_hook() as a string.  
 * 
 * This is invoked in the same way as fire_plugin_hook().
 * 
 * @uses fire_plugin_hook()
 * @return string
 **/
function get_plugin_hook_output() 
{
    $args = func_get_args();
    ob_start();
    call_user_func_array('fire_plugin_hook', $args);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

/**
 * @access private
 * @return Omeka_Plugin_Broker|null
 **/
function get_plugin_broker()
{
    return Omeka_Context::getInstance()->getPluginBroker();
}

/**
 * Retrieves specified descriptive info for a plugin from its ini file.
 *
 * @param string $plugin The name of the plugin
 * @param string $key
 * @return string The value of the specified plugin key. If the key does not exist, it returns an empty string.
 **/
function get_plugin_ini($plugin, $key)
{            
    $path = PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . 'plugin.ini';
    if (file_exists($path)) {
        try {
            $config = new Zend_Config_Ini($path, 'info');
        } catch(Exception $e) {
			throw $e;
		}   
    } else {
		throw new Exception("Path to plugin.ini for '$plugin' is not correct.");
	}

    return $config->$key;
}

/**
 * Declare a function that will be used to display files with a given MIME type.
 * 
 * @uses Omeka_Plugin_Broker::addMediaAdapter() See for info on arguments and
 * usage.
 * @return void
 **/
function add_mime_display_type($mimeTypes, $callback, array $options=array())
{
    get_plugin_broker()->addMediaAdapter($mimeTypes, $callback, $options);
}

/**
 * Apply a set of plugin filters to a given value.  
 * 
 * The first two arguments represent the name of the filter and the value to 
 * filter, and all subsequent arguments are passed to the individual filter 
 * implementations.
 * 
 * @since 0.10
 * @uses Omeka_Plugin_Filters::applyFilters()
 * @param string|array $filterName
 * @param mixed $valueToFilter
 * @return mixed
 **/
function apply_filters($filterName, $valueToFilter)
{
    if ($pluginBroker = get_plugin_broker()) {
        $extraOptions = array_slice(func_get_args(), 2);
        return $pluginBroker->applyFilters($filterName, $valueToFilter, $extraOptions);
    }
    
    // If the plugin broker is not enabled for this request (possibly for testing), return the original value.
    return $valueToFilter;
}

/**
 * Declare a filter implementation.
 * 
 * @since 0.10
 * @param string|array $filterName
 * @param callback $callback
 * @param integer $priority Optional Defaults to 10.
 * @return void
 **/
function add_filter($filterName, $callback, $priority = 10)
{
    if ($pluginBroker = get_plugin_broker()) {
        $pluginBroker->addFilter($filterName, $callback, $priority);
    }
}

/**
 * Retrieve the ACL object.
 * 
 * @return Omeka_Acl
 **/
function get_acl()
{
    return Omeka_Context::getInstance()->getAcl();
}

/**
 * Determine whether or not the script is being executed through the 
 * administrative interface.
 * 
 * Can be used to branch behavior based on whether or not the admin theme is 
 * being accessed, but should not be relied upon in place of using the ACL for
 * controlling access to scripts.
 * 
 * @return boolean
 **/
function is_admin_theme()
{
    return defined('ADMIN');
}

/**
 * Insert a new item into the Omeka database.
 *
 * @uses InsertItemHelper For more information on arguments and usage.
 * @param array $metadata Optional
 * @param array $elementTexts Optional
 * @param array $fileMetadata Optional
 * @return Item
 */
function insert_item($metadata = array(), $elementTexts = array(), $fileMetadata = array())
{    
    // Passing null means this will create a new item.
    $helper = new InsertItemHelper(null, $metadata, $elementTexts, $fileMetadata);
    $helper->run();
    return $helper->getItem();
}

/**
 * Add files to an item.
 * 
 * @uses InsertItemHelper::addFiles() See for information on arguments and notes
 * on usage.
 * @param Item|integer $item
 * @param string|Omeka_File_Ingest_Abstract $transferStrategy
 * @param array $files
 * @param array $options Optional
 * @return array
 **/
function insert_files_for_item($item, $transferStrategy, $files, $options = array())
{
    $helper = new InsertItemHelper($item);
    return $helper->addFiles($transferStrategy, $files, $options);
}

/**
 * @see insert_item()
 * @uses InsertItemHelper
 * @param Item|int $item Either an Item object or the ID for the item.
 * @param array $metadata Set of options that can be passed to the item.
 * @param array $elementTexts
 * @param array $fileMetadata
 * @return Item
 **/
function update_item($item, $metadata = array(), $elementTexts = array(), $fileMetadata = array())
{
    $helper = new InsertItemHelper($item, $metadata, $elementTexts, $fileMetadata);
    $helper->run();
    return $helper->getItem();
}

/**
 * Insert a new item type.
 *
 * @param array $metadata Follows the format:
 * <code>
 * array(
  *     'name'       => [string], 
  *     'description'=> [string]
  * )
 * </code>
 * @param array $elementInfos Follows the format:
 * <code>
 * array(
 *      [element name] => array(
 *             'description' => [string],
 *             'data_type_name' => [string]
 *         ), 
 *      [element name] => array(
 *             'description' => [string],
 *             'data_type_name' => [string]
 *         ), 
 *      [element name] => array(
 *             'description' => [string],
 *             'data_type_name' => [string]
 *         ), 
 *         [element name] => array(
 *             'description' => [string],
 *             'data_type_name' => [string]
 *        )
 *    )
 * </code>
 * @return ItemType
 * @throws Exception
 **/
function insert_item_type($metadata = array(), $elementInfos = array()) {
    
    $settableMetadata = array('name', 'description');
    
    // make sure the item type does not already exist
    $db = get_db();
    $itemType = $db->getTable('ItemType')->findBySql('name = ?', array($metadata['name']), true) ;
    
    if (!empty($itemType)) {
        throw new Exception('Cannot insert item type ' . $metadata['name'] . ' because it already exists.');
    }
    
    $itemType = new ItemType;
    foreach ($settableMetadata as $value) {
        if (array_key_exists($value, $metadata)) {
            $itemType->$value = $metadata[$value];
        }
    }
    $itemType->forceSave();
    
    foreach($elementInfos as $elementName => $elementConfig) {        
        $elementDescription = $elementConfig['description'];
        $elementDataTypeName = $elementConfig['data_type_name'];
        $itemType->addElementByName($elementName, $elementDescription, $elementDataTypeName);   
    }
    
    return $itemType;    
}


/**
 * @param array $metadata Follows the format:
 * <code> array(
 *     'name'       => [string], 
 *     'description'=> [string], 
 *     'public'     => [true|false], 
 *     'featured'   => [true|false]
 * )</code>
 */
function insert_collection($metadata = array())
{
    $collection = new Collection;
    
    $settableMetadata = array('name', 'description', 'public', 'featured');
    
    foreach ($settableMetadata as $value) {
        if (array_key_exists($value, $metadata)) {
            $collection->$value = $metadata[$value];
        }
    }
    $collection->save();
    
    return $collection;
}

/**
 * Insert an element set and its elements into the database.
 * 
 * @param string|array $elementSet Element set information.
 * <code>
 *     [(string) element set name]
 *     -OR-
 *     array(
 *         'name'        => [(string) element set name, required, unique], 
 *         'description' => [(string) element set description, optional]
 *     );
 * </code>
 * @param array $elements An array containing element data. There are three 
 * ways to include elements:
 * <ol>
 * <li>An array containing element data</li>
 * <li>A string of the element name</li>
 * <li>A new or existing Element record object</li>
 * </ol>
 * <code> 
 *    array(
 *         array(
 *             'name'        => [(string) name, required], 
 *             'description' => [(string) description, optional], 
 *             'record_type' => [(string) record type name, optional], 
 *             'data_type'   => [(string) data type name, optional], 
 *             'order'       => [(int) order, optional]
 *         ), 
 *         [(string) element name], 
 *         [(object) Element]
 *     );
 * </code>
 * @return ElementSet
 */
function insert_element_set($elementSet, array $elements = array())
{
    // Process an element set array.
    if (is_array($elementSet)) {
        
        // Trim whitespace from all array elements.
        array_walk($elementSet, 'trim');
        
        // Set the element set name.
        if (!isset($elementSet['name'])) {
            throw new Exception('An element set name was not given.');
        }
        $elementSetName = $elementSet['name'];
        
        // Set the element set description.
        $elementSetDescription = isset($elementSet['description']) 
                                 ? $elementSet['description'] : null;
        
    // Process an element string.
    } else if (is_string($elementSet)) {
        $elementSetName = $elementSet;
        $elementSetDescription = null;
    }
    
    // Instantiate a new element set record.
    $es = new ElementSet;
    
    // Set the element set name and description.
    $es->name        = $elementSetName;
    $es->description = $elementSetDescription;
    
    // Add elements to the element set.
    $es->addElements($elements);
    
    // Save the element set.
    $es->save();
    
    return $es;
}

/**
 * Releases an object from memory.
 * 
 * Use this fuction after you are done using an Omeka model object to prevent 
 * memory leaks.  Required because PHP 5.2 does not do garbage collection on 
 * circular references.
 *
 * @param mixed 
 */
function release_object(&$var) 
{
    if (is_array($var)) {
        array_walk($var, 'release_object');
    } else if (is_object($var)) {
        $var->__destruct();
        unset($var);  
    }
}