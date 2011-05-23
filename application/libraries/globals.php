<?php
/**
 * Helper functions that are always available in Omeka.  As global functions,
 * these should be used as little as possible in the application code
 * to reduce coupling.
 *
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Retrieve an option from the Omeka database.
 * 
 * If the returned value represents an object or array, it must be unserialized
 * by the caller before use.  For example, 
 * <code>$object = unserialize(get_option('plugin_object'))</code>.
 * 
 * @param string $name
 * @return string
 */ 
function get_option($name) 
{
    $options = Omeka_Context::getInstance()->getOptions();
    if (isset($options[$name])) {
        return $options[$name];
    }
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
 */
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
 */
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
 * Trims whitespace, replaces disallowed characters with hyphens,
 * converts the resulting string to lowercase, and trims at 30 characters.
 * 
 * @param string $text
 * @return string
 */
function generate_slug($text)
{
    // Remove characters other than alphanumeric, hyphen, underscore.
    $slug = preg_replace('/[^a-z0-9\-_]/', '-', strtolower(trim($text)));
    // Trim down to 30 characters.
    return substr($slug, 0, 30);
}

/**
 * Retrieve one column of a multidimensional array as an array.
 * 
 * @param string|integer $col
 * @param array $array
 * @return array
 */
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
 */
function current_user()
{
    return Omeka_Context::getInstance()->getCurrentUser();
}

/**
 * Retrieve the database object.
 * 
 * @return Omeka_Db
 */
function get_db()
{
    return Omeka_Context::getInstance()->getDb();
}

/**
 * Log a message with 'DEBUG' priority.
 * 
 * @uses _log() 
 * @param string $msg
 * @return void
 */
function debug($msg)
{
    _log($msg, Zend_Log::DEBUG);
}

/**
 * Log a message.
 *
 * Enabled via config.ini: log.errors.
 *
 * @param mixed $msg
 * @param integer $priority Optional Defaults to Zend_Log::INFO.  See Zend_Log 
 * for a list of available priorities.
 * @return void
 */
function _log($msg, $priority = Zend_Log::INFO)
{
    $log = Omeka_Context::getInstance()->logger;
    if (!$log) {
        return;
    }
    $log->log($msg, $priority);
}

/**
 * Called during startup to strip out slashes from the request superglobals in 
 * order to avoid problems with PHP's magic_quotes setting.
 * 
 * Does not need to be called elsewhere in the application.
 * 
 * @access private
 * @param mixed $value
 * @return mixed
 */
function stripslashes_deep($value)
{
     $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);

     return $value;
}

/**
 * Declare a plugin hook implementation within a plugin.
 * 
 * @param string $hook Name of hook being implemented.
 * @param mixed $callback Any valid PHP callback.
 * @return void
 */
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
 * <code>
 * // Calls the hook 'after_save_item' with the arguments '$item' and '$arg2'
 * fire_plugin_hook('after_save_item', $item, $arg2);
 * </code>
 *
 * @param string $hookName
 * @param mixed $args,... (optional) Any arguments to be passed to hook 
 * implementations.
 * @return mixed
 */
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
 * @param string $hookName
 * @param mixed $args,... (optional) Any arguments to be passed to hook 
 * implementations.
 * @return string
 */
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
 * @see get_plugin_hook_output()
 * @param string $pluginName
 * @param string $hookName
 * @param mixed $args,... (optional) Any arguments to be passed to the hook 
 * implementation.
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
 * Retrieve the broker object for Omeka plugins.
 *
 * @access private
 * @return Omeka_Plugin_Broker|null
 */
function get_plugin_broker()
{
    if (Zend_Registry::isRegistered('pluginbroker')) {
        return Zend_Registry::get('pluginbroker');
    }
}

/**
 * Retrieve specified descriptive info for a plugin from its ini file.
 *
 * @param string $pluginDirName The directory name of the plugin.
 * @param string $iniKeyName The name of the key in the ini file.
 * @return string|null The value of the specified plugin key. If the key does 
 * not exist, it returns null.
 */
function get_plugin_ini($pluginDirName, $iniKeyName)
{         
   $pluginIniReader = Zend_Registry::get('plugin_ini_reader');
   if ($pluginIniReader->hasPluginIniFile($pluginDirName)) {
       return $pluginIniReader->getPluginIniValue($pluginDirName, $iniKeyName);
   }
}

/**
 * Declare a function that will be used to display files with a given MIME type.
 * 
 * @uses Omeka_View_Helper_Media::addMimeTypes() See for info on arguments and
 * usage.
 * @param array|string $mimeTypes Set of MIME types that this specific
 * callback will respond to.
 * @param callback $callback Any valid callback.
 * @param array $options
 * @return void
 */
function add_mime_display_type($mimeTypes, $callback, array $options=array())
{
    require_once HELPER_DIR . '/' . 'Media.php';
    Omeka_View_Helper_Media::addMimeTypes($mimeTypes, $callback, $options);
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
 * @param mixed $args,... (optional) Any additional arguments to pass to filter
 * implementations.
 * @return mixed Result of applying filters to $valueToFilter.
 */
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
 * @param integer $priority Optional, Defaults to 10.
 * @return void
 */
function add_filter($filterName, $callback, $priority = 10)
{
    if ($pluginBroker = get_plugin_broker()) {
        $pluginBroker->addFilter($filterName, $callback, $priority);
    }
}

/**
 * Clear all implementations for a filter (or all filters).
 *
 * @uses Omeka_Plugin_Filters::clearFilters()
 * @param string|null $name The name of the filter to clear.  If
 *  null or omitted, all filters will be cleared.
 * @return void
 */
function clear_filters($filterName = null)
{
    if ($pluginBroker = get_plugin_broker()) {
        $pluginBroker->clearFilters($filterName);
    }
}

/**
 * Retrieve the ACL object.
 * 
 * @return Omeka_Acl
 */
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
 */
function is_admin_theme()
{
    return Zend_Controller_Front::getInstance()->getParam('admin');
}

/**
 * Insert a new item into the Omeka database.
 *
 * @param array $metadata Optional Set of metadata options for configuring the
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
 * @param array $elementTexts Optional, Array of element texts to assign to the item. 
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
 * @param array $fileMetadata Optional, Set of metadata options that allow one or more
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
    $builder = new ItemBuilder(get_db());
    $builder->setRecordMetadata($metadata);
    $builder->setElementTexts($elementTexts);
    $builder->setFileMetadata($fileMetadata);
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
 */
function insert_files_for_item($item, $transferStrategy, $files, $options = array())
{
    $builder = new ItemBuilder(get_db());
    $builder->setRecord($item);
    return $builder->addFiles($transferStrategy, $files, $options);
}

/**
 * Update an existing item.
 *
 * @see insert_item()
 * @uses ItemBuilder
 * @param Item|int $item Either an Item object or the ID for the item.
 * @param array $metadata Set of options that can be passed to the item.
 * @param array $elementTexts
 * @param array $fileMetadata
 * @return Item
 */
function update_item($item, $metadata = array(), $elementTexts = array(), $fileMetadata = array())
{
    $builder = new ItemBuilder(get_db());
    $builder->setRecord($item);
    $builder->setRecordMetadata($metadata);
    $builder->setElementTexts($elementTexts);
    $builder->setFileMetadata($fileMetadata);
    return $builder->build();
}

/**
 * Insert a new item type.
 *
 * @param array $metadata Follows the format:
 * <code>
 * array(
 *     'name'        => [string], 
 *     'description' => [string]
 * );
 * </code>
 * @param array $elementInfos An array containing element data. Each entry follows
 * one or more of the following formats:
 * <ol>
 * <li>An array containing element metadata</li>
 * <li>An Element object</li>
 * </ol>
 * <code> 
 *    array(
 *         array(
 *             'name'           => [(string) name, required], 
 *             'description'    => [(string) description, optional], 
 *             'record_type'    => [(string) record type name, optional], 
 *             'data_type'      => [(string) data type name, optional], 
 *             'order'          => [(int) order, optional],
 *             'record_type_id' => [(int) record type id, optional],
 *             'data_type_id'   => [(int) data type id, optional]
 *         ), 
 *         [(Element)], 
 *     );
 * </code>
 * @return ItemType
 * @throws Exception
 */
function insert_item_type($metadata = array(), $elementInfos = array()) 
{
    $builder = new ItemTypeBuilder(get_db());
    $builder->setRecordMetadata($metadata);
    $builder->setElements($elementInfos);    
    return $builder->build();
}

/**
 * Insert a collection
 * 
 * @param array $metadata Follows the format:
 * <code> array(
 *     'name'        => [string], 
 *     'description' => [string], 
 *     'public'      => [true|false], 
 *     'featured'    => [true|false]
 *     'collectors'  => [array of string names]
 * )</code>
 * 
 * @return Collection
 */
function insert_collection($metadata = array())
{
    $builder = new CollectionBuilder(get_db());
    $builder->setRecordMetadata($metadata);
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
 *             'name'           => [(string) name, required], 
 *             'description'    => [(string) description, optional], 
 *             'record_type'    => [(string) record type name, optional], 
 *             'data_type'      => [(string) data type name, optional], 
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
    $builder = new ElementSetBuilder(get_db());
    $builder->setRecordMetadata($elementSetMetadata);
    $builder->setElements($elements);
    return $builder->build();
}

/**
 * Release an object from memory.
 * 
 * Use this fuction after you are done using an Omeka model object to prevent 
 * memory leaks.  Required because PHP 5.2 does not do garbage collection on 
 * circular references.
 *
 * @param mixed &$var The object to be released, or an array of such objects.
 * @return void
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
 * Return either the value passed or, if it's empty, return a default value.
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
 */
function is_true($value) 
{
    if ($value === null) {
        return false;
    }
    $value = strtolower(trim($value));
    return ($value == '1' || $value == 'true');
}

/**
 * Gets a theme option
 * 
 * @param string $optionName The name of the option to get.
 * @param string $themeName The name of the theme.  If null, it will use the 
 * current public theme.
 * @return string The value of the theme option.
 */
function get_theme_option($optionName, $themeName = null)
{
    if (!$themeName) {
        $themeName = Theme::getCurrentThemeName('public');
    }
    return Theme::getOption($themeName, $optionName);
}

/**
 * Sets a theme option
 * 
 * @param string $optionName The name of the option to set.
 * @param string $optionValue The value of the option.
 * @param string $themeName The name of the theme.  If null, it will use the 
 * current public theme.
 * @return void
 */
function set_theme_option($optionName, $optionValue, $themeName = null)
{
    if (!$themeName) {
        $themeName = Theme::getCurrentThemeName('public');
    }
    return Theme::setOption($themeName, $optionName, $optionValue);
}

/**
 * Returns an array of all user role names.
 * 
 * @return array
 */
function get_user_roles()
{
	$roles = Omeka_Context::getInstance()->getAcl()->getRoleNames();
	foreach($roles as $key => $val) {
		$roles[$val] = Inflector::humanize($val);
		unset($roles[$key]);
	}
	return $roles;
}

/**
 * Determines whether an Element Set contains a specific Element
 *
 * @since 1.3
 * @param string $elementSetName The name of the element set.
 * @param string $elementName The name of the element.
 * @return bool
 **/
function element_exists($elementSetName, $elementName) {
    $element = get_db()->getTable('Element')->findByElementSetNameAndElementName($elementSetName, $elementName);
    return (bool)$element;
} 


/**
 * Determine whether or not a plugin is installed and active.  
 *
 * May be used by theme/plugin writers to customize behavior based on the
 * existence of certain plugins.
 *
 * Some examples of how to use this function:
 *
 * Check if ExhibitBuilder is installed and activated.
 * <code>
 * if (plugin_is_active('ExhibitBuilder')):
 * </code>
 *
 * Check if installed version of ExhibitBuilder is at least version 1.0 or 
 * higher.
 * <code> 
 * if (plugin_is_active('ExhibitBuilder', '1.0')): 
 * </code>
 *
 * Check if installed version of ExhibitBuilder is anything less than 2.0.
 * <code>
 * if (plugin_is_active('ExhibitBuilder', '2.0', '<')): 
 * </code>
 *
 * @param string $name Directory name of the plugin.
 * @param string $version Optional Version of the plugin to check.  
 * @param string $compOperator Optional Comparison operator to use when 
 * checking the installed version of ExhibitBuilder.  Defaults to '>=' (to 
 * check verify installed version).
 * @return boolean
 */
function plugin_is_active($name, $version = null, $compOperator = '>=')
{
    $plugin = get_db()->getTable('Plugin')->findByDirectoryName($name);
    if (!$plugin) {
        return false;
    }
    if (!$plugin->isActive()) {
        return false;
    }
    if ($version) {
        return version_compare($plugin->getDbVersion(), $version, $compOperator);
    } else {
        return true;
    } 
}
