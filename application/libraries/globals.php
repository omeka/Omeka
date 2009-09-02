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
 * Retrieve the output of a specific plugin's hook as a string. 
 * 
 * This is like get_plugin_hook_output() but only calls the hook within the 
 * provided plugin.
 * 
 * @param string $pluginName
 * @param string $hookName
 * @param [any number of further arguments]
 * @return string
 */
function get_specific_plugin_hook_output()
{
    $args = func_get_args();
    
    // Get the plugin name (1st arg) and hook name (2nd arg).
    $pluginName = array_shift($args);
    $hookName = array_shift($args);
    
    // Get the specific hook.
    $pluginBroker = get_plugin_broker();
    $hookNameSpecific = $pluginBroker->getHook($pluginName, $hookName);
    
    // Return null if the specific hook doesn't exist.
    if (!$hookNameSpecific) {
        return null;
    }
    
    // Buffer and return any output originating from the hook.
    ob_start();
    call_user_func_array($hookNameSpecific, $args);
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
    if (Zend_Registry::isRegistered('pluginbroker')) {
        return Zend_Registry::get('pluginbroker');
    }
}

/**
 * Retrieves specified descriptive info for a plugin from its ini file.
 *
 * @param string $pluginDirName The directory name of the plugin
 * @param string $iniKeyName The name of the key in the ini file
 * @return null | string The value of the specified plugin key. If the key does not exist, it returns null
 **/
function get_plugin_ini($pluginDirName, $iniKeyName)
{         
   $pluginBroker = Omeka_Context::getInstance()->getPluginBroker();
   return $pluginBroker->getPluginIniValue($pluginDirName, $iniKeyName);
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
 * * @param array $itemMetadata Optional Set of metadata options for configuring the
 *  item.  Array which can include the following properties:
 *  <ul>
 *      <li>'public' (boolean)</li>
 *      <li>'featured' (boolean)</li>
 *      <li>'collection_id' (integer)</li>
 *      <li>'item_type_id' (integer)</li>
 *      <li>'item_type_name' (string)</li>
 *      <li>'tags' (string, comma-delimited)</li>
 *      <li>'tag_entity' (Entity, optional and only checked if 'tags' is given)</li>
 *      <li>'overwriteElementTexts' (boolean) -- determines whether or not to
 *  overwrite existing element texts.  If true, this will loop through the
 *  element texts provided in $elementTexts, and it will update existing
 *  records where possible.  All texts that are not yet in the DB will be
 *  added in the usual manner.  False by default.</li>
 *  </ul> 
 * 
 * @param array $elementTexts Optional Array of element texts to assign to the item. 
 *  This follows the format: 
 * <code>
 * array(
  *     [element set name] => array(
  *         [element name] => array(
  *             array('text' => [string], 'html' => [false|true]), 
  *             array('text' => [string], 'html' => [false|true])
  *         ), 
  *         [element name] => array(
  *             array('text' => [string], 'html' => [false|true]), 
  *             array('text' => [string], 'html' => [false|true])
  *         )
  *     ), 
  *     [element set name] => array(
  *         [element name] => array(
  *             array('text' => [string], 'html' => [false|true]), 
  *             array('text' => [string], 'html' => [false|true])
  *         ), 
  *         [element name] => array(
  *             array('text' => [string], 'html' => [false|true]), 
  *             array('text' => [string], 'html' => [false|true])
  *         )
  *     )
  * );
  * </code>
 *  See ActsAsElementText::addElementTextsByArray() for more info.
 * 
 * @param array $fileMetadata Optional Set of metadata options that allow one or more
 * files to be associated with the item.  Includes the following options:
 *  <ul>    
 *      <li>'file_transfer_type' (string = 'Url|Filesystem|Upload' or 
 * Omeka_File_Transfer_Adapter_Interface).  Corresponds to the 
 * $transferStrategy argument for addFiles().</li>
 *      <li>'file_ingest_options' OPTIONAL (array of possible options to pass
 * modify the behavior of the ingest script).  Corresponds to the $options 
 * argument for addFiles().</li>
 *      <li>'files' (array or string) Represents information indicating the file
 * to ingest.  Corresponds to the $files argument for addFiles().</li>
 * </ul>
 * @uses ItemBuilder For more information on arguments and usage.
 * @see ActsAsElementText::addElementTextsByArray()
 * @return Item
 */
function insert_item($metadata = array(), $elementTexts = array(), $fileMetadata = array())
{    
    // Passing null means this will create a new item.
    $builder = new ItemBuilder($metadata, $elementTexts, $fileMetadata);
    return $builder->build();
}

/**
 * Add files to an item.
 * 
 * @uses ItemBuilder::addFiles() See for information on arguments and notes
 * on usage.
 * @param Item|integer $item
 * @param string|Omeka_File_Ingest_Abstract $transferStrategy
 * @param array $files
 * @param array $options Optional
 * @return array
 **/
function insert_files_for_item($item, $transferStrategy, $files, $options = array())
{
    // TODO: Maybe this should be a separate helper class.
    $helper = new ItemBuilder(array(), array(), array(), $item);
    return $helper->addFiles($transferStrategy, $files, $options);
}

/**
 * @see insert_item()
 * @uses ItemBuilder
 * @param Item|int $item Either an Item object or the ID for the item.
 * @param array $metadata Set of options that can be passed to the item.
 * @param array $elementTexts
 * @param array $fileMetadata
 * @return Item
 **/
function update_item($item, $metadata = array(), $elementTexts = array(), $fileMetadata = array())
{
    $builder = new ItemBuilder($metadata, $elementTexts, $fileMetadata, $item);
    return $builder->build();
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
 * @param array $elementInfos An array containing element data. Each entry follows
 * one or more of the following formats:
 * <ol>
 * <li>An array containing element metadata</li>
 * <li>A string containing the element name</li>
 * </ol>
 * <code> 
 *    array(
 *         array(
 *             'name'        => [(string) name, required], 
 *             'description' => [(string) description, optional], 
 *             'record_type' => [(string) record type name, optional], 
 *             'data_type'   => [(string) data type name, optional], 
 *             'order'       => [(int) order, optional],
 *             'record_type_id' => [(int) record type id, optional],
 *             'data_type_id'   => [(int) data type id, optional]
 *         ), 
 *         [(string) element name], 
 *     );
 * </code>
 * @return ItemType
 * @throws Exception
 **/
function insert_item_type($metadata = array(), $elementInfos = array()) {
    $builder = new ItemTypeBuilder($metadata, $elementInfos);    
    return $builder->build();
}


/**
 * Inserts a collection
 * 
 * @param array $metadata Follows the format:
 * <code> array(
 *     'name'       => [string], 
 *     'description'=> [string], 
 *     'public'     => [true|false], 
 *     'featured'   => [true|false]
 *     'collectors' => [array of entities, entity ids, or entity property arrays]
 * )</code>
 * 
 * You can specify collectors in several ways.
 *
 * You can provide an array of entity properties:
 * <code>
 * insert_collection(array('collectors'=>array(
 *   array('first_name' => $entityFirstName1,
 *         'middle_name' => $entityMiddleName1, 
 *         'last_name' => $entityLastName1,
 *          ...
 *         ),
 *   array('first_name' => $entityFirstName2,
 *         'middle_name' => $entityMiddleName2, 
 *         'last_name' => $entityLastName2,
 *         ...
 *         ),
 *   array(...),
 *   ...
 * ));
 * </code>
 *
 * Alternatively, you can use an array of entity objects or entity ids.
 *
 *  insert_collection(array('collectors'=>array($entity1, $entity2, ...));
 *  insert_collection(array('collectors'=>array($entityId1, $entityId2, ...));
 *
 * Also you can mix the parameters:
 *
 * <code>
 * insert_collection(array('collectors'=>array(
 *    array('first_name' => $entityFirstName1,
 *         'middle_name' => $entityMiddleName1, 
 *         'last_name' => $entityLastName1,
 *          ...
 *         ),
 *   $entity2,
 *   $entityId3,
 *   ...
 * ));
 * </code> 
 * 
 **/
function insert_collection($metadata = array())
{
    $builder = new CollectionBuilder($metadata);
    return $builder->build();
}

/**
 * Insert an element set and its elements into the database.
 * 
 * @param string|array $elementSetMetadata Element set information.
 * <code>
 *     [(string) element set name]
 *     -OR-
 *     array(
 *         'name'        => [(string) element set name, required, unique], 
 *         'description' => [(string) element set description, optional]
 *     );
 * </code>
 * @param array $elements An array containing element data. Follows one of more
 * of the following formats:
 * <ol>
 * <li>An array containing element metadata</li>
 * <li>A string of the element name</li>
 * </ol>
 * <code> 
 *    array(
 *         array(
 *             'name'        => [(string) name, required], 
 *             'description' => [(string) description, optional], 
 *             'record_type' => [(string) record type name, optional], 
 *             'data_type'   => [(string) data type name, optional], 
 *             'record_type_id' => [(int) record type id, optional],
 *             'data_type_id'   => [(int) data type id, optional]
 *         ), 
 *         [(string) element name]
 *     );
 * </code>
 * @return ElementSet
 */
function insert_element_set($elementSetMetadata = array(), array $elements = array())
{
    $builder = new ElementSetBuilder($elementSetMetadata, $elements);
    return $builder->build();
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
    }
    $var = null;
}

/**
 * Return either the value or, if it's empty, output the default.
 * 
 * @param mixed $value
 * @param mixed $default
 * @return mixed
 */
function not_empty_or($value, $default) 
{
    return !empty($value) ? $value : $default;
}



/**
  * Returns whether a value is true or not.  
  * If the value is a string and its lowercased value is 'true' or '1', it returns true.
  * If the value is an integer and equal to 1, then it returns true.
  * Otherwise it returns false.
  * @param string $value
  * @return boolean
  **/
function is_true($value) 
{
    if ($value === null) {
        return false;
    }
    $value = strtolower(trim($value));
    return ($value == '1' || $value == 'true');
}