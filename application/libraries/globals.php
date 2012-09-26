<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Return an option from the options table.
 *
 * If the returned value represents an object or array, it must be unserialized
 * by the caller before use. For example:
 * <code>$object = unserialize(get_option('plugin_object'))</code>.
 *
 * @param string $name The option name.
 * @return string The option value.
 */
function get_option($name)
{
    $options = Zend_Registry::get('bootstrap')->getResource('Options');
    if (isset($options[$name])) {
        return $options[$name];
    }
}

/**
 * Set an option to the options table.
 *
 * Note that objects and arrays must be serialized before being saved.
 *
 * @param string $name The option name.
 * @param string $value The option value.
 */
function set_option($name, $value)
{
    $db = get_db();
    $sql = "REPLACE INTO {$db->Option} (name, value) VALUES (?, ?)";
    $db->query($sql, array($name, $value));
    
    // Update the options cache.
    $bootstrap = Zend_Registry::get('bootstrap');
    $options = $bootstrap->getResource('Options');
    $options[$name] = $value;
    $bootstrap->getContainer()->options = $options;
}

/**
 * Delete an option from the options table.
 *
 * @param string $name The option name.
 */
function delete_option($name)
{
    $db = get_db();
    $sql = "DELETE FROM {$db->Option} WHERE `name` = ?";
    $db->query($sql, array($name));
    
    // Update the options cache.
    $bootstrap = Zend_Registry::get('bootstrap');
    $options = $bootstrap->getResource('Options');
    if (isset($options[$name])) {
        unset($options[$name]);
    }
    $bootstrap->getContainer()->options = $options;
}

/**
 * Return one column of a multidimensional array as an array.
 *
 * @param string|integer $col The column to pluck.
 * @param array $array The array from which to pluck.
 * @return array The column as an array.
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
 * Return the currently logged in User record.
 *
 * @return User|null Null if no user is logged in.
 */
function current_user()
{
    return Zend_Registry::get('bootstrap')->getResource('CurrentUser');
}

/**
 * Return the database object.
 *
 * @throws RuntimeException
 * @return Omeka_Db
 */
function get_db()
{
    $db = Zend_Registry::get('bootstrap')->getResource('Db');
    if (!$db) {
        throw new RuntimeException("Database not available!");
    }
    return $db;
}

/**
 * Log a message with 'DEBUG' priority.
 *
 * @uses _log()
 * @param string $msg
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
 * @param mixed $msg The log message.
 * @param integer $priority See Zend_Log for a list of available priorities.
 */
function _log($msg, $priority = Zend_Log::INFO)
{
    try {
        $bootstrap = Zend_Registry::get('bootstrap');
    } catch (Zend_Exception $e) {
        return;
    }
    
    if (!($log = $bootstrap->getResource('Logger'))) {
        return;
    }
    $log->log($msg, $priority);
}

/**
 * Declare a plugin hook implementation within a plugin.
 *
 * @param string $hook Name of hook being implemented.
 * @param mixed $callback Any valid PHP callback.
 */
function add_plugin_hook($hook, $callback)
{
    get_plugin_broker()->addHook($hook, $callback);
}

/**
 * Declare the point of execution for a specific plugin hook.
 *
 * All plugin implementations of a given hook will be executed when this is 
 * called. The first argument corresponds to the string name of the hook. The 
 * second is an associative array containing arguments that will be passed to 
 * the plugin hook implementations.
 *
 * <code>
 * // Calls the hook 'after_save_item' with the arguments '$item' and '$arg2'
 * fire_plugin_hook('after_save_item', array('item' => $item, 'foo' => $arg2));
 * </code>
 *
 * @param string $name The hook name.
 * @param array $args Arguments to be passed to the hook implementations.
 * @return mixed
 */
function fire_plugin_hook($name, array $args = array())
{
    if ($pluginBroker = get_plugin_broker()) {
        return $pluginBroker->callHook($name, $args);
    }
}

/**
 * Return the output of fire_plugin_hook() as a string.
 * 
 * @uses fire_plugin_hook()
 * @param string $name The hook name.
 * @param array $args Arguments to be passed to the hook implementations.
 * @return string
 */
function get_plugin_hook_output($name, array $args = array())
{
    ob_start();
    fire_plugin_hook($name, $args);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

/**
 * Return the output of a specific plugin's hook as a string.
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
 * Return the broker object for Omeka plugins.
 *
 * @return Omeka_Plugin_Broker|null
 */
function get_plugin_broker()
{
    try {
        return Zend_Registry::get('pluginbroker');
    } catch (Zend_Exception $e) {
        return null;
    }
}

/**
 * Return specified descriptive info for a plugin from its ini file.
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
 * Declare a callback function that will be used to display files with a given
 * MIME type and/or file extension.
 *
 * @uses Omeka_View_Helper_FileMarkup::addMimeTypes() See for info on usage.
 * @param array|string $fileIdentifiers Set of MIME types and/or file extensions
 * to which the provided callback will respond.
 * @param callback $callback Any valid callback.
 * @param array $options
 */
function add_file_display_callback($fileIdentifiers, $callback, array $options = array())
{
    Omeka_View_Helper_FileMarkup::addMimeTypes($fileIdentifiers, $callback, $options);
}

/**
 * Apply a set of plugin filters to a given value.
 *
 * @uses Omeka_Plugin_Filters::applyFilters()
 * @param string|array $name The filter name.
 * @param mixed $value The value to filter.
 * @param array $args Additional arguments to pass to filter implementations.
 * @return mixed Result of applying filters to $value.
 */
function apply_filters($name, $value, array $args = array())
{
    if ($pluginBroker = get_plugin_broker()) {
        return $pluginBroker->applyFilters($name, $value, $args);
    }
    // If the plugin broker is not enabled for this request (possibly for 
    // testing), return the original value.
    return $value;
}

/**
 * Declare a filter implementation.
 *
 * @param string|array $name The filter name.
 * @param callback $callback The function to call.
 * @param integer $priority Optional, Defaults to 10.
 */
function add_filter($name, $callback, $priority = 10)
{
    if ($pluginBroker = get_plugin_broker()) {
        $pluginBroker->addFilter($name, $callback, $priority);
    }
}

/**
 * Clear all implementations for a filter (or all filters).
 *
 * @uses Omeka_Plugin_Filters::clearFilters()
 * @param string|null $name The name of the filter to clear.  If
 *  null or omitted, all filters will be cleared.
 */
function clear_filters($filterName = null)
{
    if ($pluginBroker = get_plugin_broker()) {
        $pluginBroker->clearFilters($filterName);
    }
}

/**
 * Return the ACL object.
 *
 * @return Zend_Acl
 */
function get_acl()
{
    return Zend_Registry::get('bootstrap')->getResource('Acl');
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
 * @uses Builder_Item For more information on arguments and usage.
 * @see ActsAsElementText::addElementTextsByArray()
 * @return Item
 */
function insert_item($metadata = array(), $elementTexts = array(), $fileMetadata = array())
{
    $builder = new Builder_Item(get_db());
    $builder->setRecordMetadata($metadata);
    $builder->setElementTexts($elementTexts);
    $builder->setFileMetadata($fileMetadata);
    return $builder->build();
}

/**
 * Add files to an item.
 *
 * @uses Builder_Item::addFiles() See for information on arguments and notes
 * on usage.
 * @param Item|integer $item
 * @param string|Omeka_File_Ingest_AbstractIngest $transferStrategy
 * @param array $files
 * @param array $options Optional
 * @return array
 */
function insert_files_for_item($item, $transferStrategy, $files, $options = array())
{
    $builder = new Builder_Item(get_db());
    $builder->setRecord($item);
    return $builder->addFiles($transferStrategy, $files, $options);
}

/**
 * Update an existing item.
 *
 * @see insert_item()
 * @uses Builder_Item
 * @param Item|int $item Either an Item object or the ID for the item.
 * @param array $metadata Set of options that can be passed to the item.
 * @param array $elementTexts
 * @param array $fileMetadata
 * @return Item
 */
function update_item($item, $metadata = array(), $elementTexts = array(), $fileMetadata = array())
{
    $builder = new Builder_Item(get_db());
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
 *             'order'          => [(int) order, optional],
 *         ),
 *         [(Element)],
 *     );
 * </code>
 * @return ItemType
 */
function insert_item_type($metadata = array(), $elementInfos = array())
{
    $builder = new Builder_ItemType(get_db());
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
    $builder = new Builder_Collection(get_db());
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
 *         'name'           => [(string) element set name, required, unique],
 *         'description'    => [(string) element set description, optional],
 *         'record_type'    => [(string) record type name, optional]
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
 *         ),
 *         [(string) element name]
 *     );
 * </code>
 * @return ElementSet
 */
function insert_element_set($elementSetMetadata = array(), array $elements = array())
{
    $builder = new Builder_ElementSet(get_db());
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
    $roles = get_acl()->getRoles();
    foreach($roles as $key => $val) {
        $roles[$val] = __(Inflector::humanize($val));
        unset($roles[$key]);
    }
    return $roles;
}

/**
 * Determines whether an Element Set contains a specific Element
 *
 * @param string $elementSetName The name of the element set.
 * @param string $elementName The name of the element.
 * @return bool
 */
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

/**
 * Translate a string.
 *
 * @param string $string The string to be translated.
 * @param mixed $args Optional string formatting args. If any extra args are
 *  passed, the args and the translated string will be formatted with
 *  sprintf().
 * @return string The translated string.
 */
function __($string)
{
    // Avoid getting the translate object more than once.
    static $translate;

    if (!isset($translate)) {
        try {
            $translate = Zend_Registry::get('Zend_Translate');
        } catch (Zend_Exception $e) {
            $translate = false;
        }
    }

    if ($translate) {
        $string = $translate->translate($string);
    }

    $args = func_get_args();

    array_shift($args);

    if (!empty($args)) {
        return vsprintf($string, $args);
    }

    return $string;
}

/**
 * Add an translation source directory.
 *
 * The directory's contents should be .mo files following the naming
 * scheme of Omeka's application/languages directory. If a .mo for the
 * current locale exists, the translations will be loaded.
 *
 * @param string $dir Directory to load translations from.
 */
function add_translation_source($dir)
{
    try {
        $translate = Zend_Registry::get('Zend_Translate');
        $locale = Zend_Registry::get('Zend_Locale');
    } catch (Zend_Exception $e) {
        return;
    }

    $locale = $locale->toString();

    try {
        $translate->addTranslation(array(
            'content' => "$dir/$locale.mo",
            'locale' => $locale
        ));
    } catch (Zend_Translate_Exception $e) {
        // Do nothing, allow the user to set a locale or dir without a
        // translation.
    }
}

/**
 * Get the correct HTML "lang" attribute for the current locale.
 *
 * @return string
 */
function get_html_lang()
{
    try {
        $locale = Zend_Registry::get('Zend_Locale');
    } catch(Zend_Exception $e) {
        return 'en-US';
    }

    return str_replace('_', '-', $locale->toString());
}


/**
 * Format a date for output according to the current locale.
 *
 * @param mixed $date Date to format. If an integer, the date is intepreted
 *  as a Unix timestamp. If a string, the date is interpreted as an ISO 8601
 *  date.
 * @param string $format Format to apply. See Zend_Date for possible formats.
 *  The default format is the current locale's "medium" format.
 *
 * @return string
 */
function format_date($date, $format = Zend_Date::DATE_MEDIUM)
{
    if (is_int($date)) {
        $sourceFormat = Zend_Date::TIMESTAMP;
    } else {
        $sourceFormat = Zend_Date::ISO_8601;
    }

    $dateObj = new Zend_Date($date, $sourceFormat);
    return $dateObj->toString($format);
}

/**
 * Declare that a JavaScript file or files will be used on the page.
 * All "used" scripts will be included in the page's head.
 *
 * This needs to be called either before head(), or in a plugin_header hook.
 *
 * @see head_js()
 * @param string|array $file File to use, if an array is passed, each array
 *  member will be treated like a file.
 * @param string $dir Directory to search for the file.  Keeping the default
 *  is recommended.
 */
function queue_js_file($file, $dir = 'javascripts')
{
    if (is_array($file)) {
        foreach($file as $singleFile) {
            queue_js_file($singleFile, $dir);
        }
        return;
    }
    get_view()->headScript()->appendFile(src($file, $dir, 'js'));
}

/**
 * Declare a JavaScript string to be used on the page and included in
 * the page's head.
 *
 * This needs to be called either before head() or in a plugin_header
 * hook.
 *
 * @see head_js()
 * @param string $string JavaScript string to include.
 */
function queue_js_string($string)
{
    get_view()->headScript()->appendScript($string);
}

/**
 * Declare that a CSS file or files will be used on the page.
 * All "used" stylesheets will be included in the page's head.
 *
 * This needs to be called either before head(), or in a plugin_header hook.
 *
 * @see head_css()
 * @param string|array $file File to use, if an array is passed, each array
 *  member will be treated like a file.
 * @param string $media CSS media declaration, defaults to 'all'.
 * @param string|bool $conditional Optional IE-style conditional comment, used
 *  generally to include IE-specific styles. Defaults to false.
 * @param string $dir Directory to search for the file.  Keeping the default
 *  is recommended.
 */
function queue_css_file($file, $media = 'all', $conditional = false, $dir = 'css')
{
    if (is_array($file)) {
        foreach($file as $singleFile) {
            queue_css_file($singleFile, $media, $conditional, $dir);
        }
        return;
    }
    get_view()->headLink()->appendStylesheet(css_src($file, $dir), $media, $conditional);
}

/**
 * Declare a CSS string to be used on the page and included in the
 * page's head.
 *
 * This needs to be called either before head() or in a plugin_header
 * hook.
 *
 * @see head_css
 * @param string $string CSS string to include.
 * @param string $media CSS media declaration, defaults to 'all'.
 * @param string|bool $conditional Optional IE-style conditional comment,
 *  used generally to include IE-specific styles. Defaults to false.
 */
function queue_css_string($string, $media = 'all', $conditional = false)
{
    $attrs = array();
    if ($media) {
        $attrs['media'] = $media;
    }
    if ($conditional) {
        $attrs['conditional'] = $conditional;
    }
    get_view()->headStyle()->appendStyle($string, $attrs);
}

/**
 * Print the JavaScript tags that will be used on the page.
 *
 * This should generally be used with echo to print the scripts in the page
 * head.
 *
 * @see queue_js_file()
 * @param bool $includeDefaults Whether the default javascripts should be
 *  included. Defaults to true.
 */
function head_js($includeDefaults = true)
{
    $headScript = get_view()->headScript();

    if ($includeDefaults) {
        $dir = 'javascripts';
        $config = Zend_Registry::get('bootstrap')->getResource('Config');
        $useInternalJs = isset($config->theme->useInternalJavascripts)
                ? (bool) $config->theme->useInternalJavascripts
                : false;

        $headScript->prependScript('jQuery.noConflict();');
        if ($useInternalJs) {
            $headScript->prependFile(src('jquery-ui', $dir, 'js'))
                       ->prependFile(src('jquery', $dir, 'js'));
        } else {
            $headScript->prependFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js')
                       ->prependFile('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
        }
    }

    return $headScript;
}

/**
 * Print the CSS link tags that will be used on the page.
 *
 * This should generally be used with echo to print the scripts in the page
 * head.
 *
 * @see queue_css_file()
 */
function head_css()
{
    return get_view()->headLink() . get_view()->headStyle();
}

/**
 * Return the web path to a css file.
 *
 * @param string $file Should not include the .css extension
 * @param string $dir Defaults to 'css'
 * @return string
 */
function css_src($file, $dir = 'css')
{
    return src($file, $dir, 'css');
}

/**
 * Return the web path to an image file.
 *
 * @param string $file Filename, including the extension.
 * @param string $dir Optional Directory within the theme to look for image
 * files.  Defaults to 'images'.
 * @return string
 */
function img($file, $dir = 'images')
{
    return src($file, $dir);
}

/**
 * Echos the web path (that's what's important to the browser)
 * to a javascript file.
 * $dir defaults to 'javascripts'
 * $file should not include the .js extension
 *
 * @param string $file The name of the file, without .js extension.
 * @param string $dir The directory in which to look for javascript files.  Recommended to leave the default value.
 */
function js_tag($file, $dir = 'javascripts')
{
    $href = src($file, $dir, 'js');

    return '<script type="text/javascript" src="' . html_escape($href) . '" charset="utf-8"></script>'."\n";
}

/**
 * Return a valid src attribute value for a given file.  Used primarily
 * by other helper functions.
 *
 *
 * @param string        Filename
 * @param string|null   Directory that the file is contained in (optional)
 * @param string        File extension (optional)
 * @return string
 */
function src($file, $dir=null, $ext = null)
{
    if ($ext !== null) {
        $file .= '.'.$ext;
    }
    if ($dir !== null) {
        $file = $dir. '/' .$file;
    }
    return web_path_to($file);
}

/**
 * Return the physical path for an asset/resource within the theme (or plugins, shared, etc.)
 *
 * @throws InvalidArgumentException
 * @param string $file
 * @return string
 */
function physical_path_to($file)
{
    $view = get_view();
    $paths = $view->getAssetPaths();

    foreach ($paths as $path) {
        list($physical, $web) = $path;
        if(file_exists($physical . '/' . $file)) {
            return $physical . '/' . $file;
        }
    }
    throw new InvalidArgumentException( __("Could not find file %s!",$file) );
}

/**
 * Return the web path for an asset/resource within the theme
 *
 * @throws InvalidArgumentException
 * @param string $file
 * @return string
 */
function web_path_to($file)
{
    $view = get_view();
    $paths = $view->getAssetPaths();
    foreach ($paths as $path) {
        list($physical, $web) = $path;
        if(file_exists($physical . '/' . $file)) {
            return $web . '/' . $file;
        }
    }
    throw new InvalidArgumentException( __("Could not find file %s!",$file) );
}

/**
 * Determine whether or not the collection has any collectors associated with it.
 *
 * @return boolean
 */
function collection_has_collectors()
{
    return get_current_record('collection')->hasCollectors();
}

/**
 * Returns the HTML markup for displaying a random featured collection.
 *
 * @return string
 */
function random_featured_collection()
{
    $featuredCollection = get_random_featured_collection();
    $html = '<h2>' . __('Featured Collection') . '</h2>';
    if ($featuredCollection) {
        $html .= '<h3>' . link_to_collection($collectionTitle, array(), 'show', $featuredCollection) . '</h3>';
        if ($collectionDescription = metadata($featuredCollection, 'Description', array('snippet'=>150))) {
            $html .= '<p class="collection-description">' . $collectionDescription . '</p>';
        }

    } else {
        $html .= '<p>' . __('No featured collections are available.') . '</p>';
    }
    return $html;
}

/**
 * Return the Collection object for the current item.
 *
 * @param Item|null Check for this specific item record (current item if null).
 * @internal This is meant to be a simple facade for access to the Collection
 * record.  Ideally theme writers won't have to interact with the actual object.
 * @return Collection
 */
function get_collection_for_item($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    return $item->Collection;
}

/**
 * Returns the most recent collections
 *
 * @param integer $num The maximum number of recent collections to return
 * @return array
 */
function get_recent_collections($num = 10)
{
    return get_records('Collection', array('sort_field' => 'added', 'sort_dir' => 'd'), $num);
}

/**
 * Returns a random featured collection.
 *
 * @return Collection
 */
function get_random_featured_collection()
{
    return get_db()->getTable('Collection')->findRandomFeatured();
}

/**
 * Returns the total number of results
 *
 * @return integer
 */
function total_results()
{
    return get_view()->total_records;
}

/**
 * Return the latest available version of Omeka by accessing the appropriate
 * URI on omeka.org.
 *
 * @return string|false The latest available version of Omeka, or false if the
 * request failed for some reason.
 */
function latest_omeka_version()
{
    $omekaApiUri = 'http://api.omeka.org/latest-version';
    $omekaApiVersion = '0.1';

    // Determine if we have already checked for the version lately.
    $check = unserialize(get_option('omeka_update')) or $check = array();
    // This a timestamp corresponding to the last time we checked for
    // a new version.  86400 is the number of seconds in a day, so check
    // once a day for a new version.
    if (array_key_exists('last_updated', $check)
        and ($check['last_updated'] + 86400) > time()) {
        // Return the value we got the last time we checked.
        return $check['latest_version'];
    }

    try {
        $client = new Zend_Http_Client($omekaApiUri);
        $client->setParameterGet('version', $omekaApiVersion);
        $client->setMethod('GET');
        $result = $client->request();
        if ($result->getStatus() == '200') {
            $latestVersion = $result->getBody();
            // Store the newer values
            $check['latest_version'] = $latestVersion;
            $check['last_updated'] = time();
            set_option('omeka_update', serialize($check));
           return $result->getBody();
        } else {
           debug("Attempt to GET $omekaApiUri with version=$omekaApiVersion "
                 . "returned with status=" . $result->getStatus() . " and "
                 . "response body=" . $result->getBody());
        }
    } catch (Zend_Http_Client_Adapter_Exception $e) {
        debug('Error in retrieving latest Omeka version: ' . $e->getMessage());
    }
    return false;
}

/**
 * Displays a set of files based on the file's MIME type and any options that are
 * passed.  This is primarily used by other helper functions and will not be used
 * by theme writers in most cases.
 * 
 * @uses Omeka_View_Helper_FileMarkup
 * @param File $files A file record or an array of File records to display.
 * @param array $props Properties to customize display for different file types.
 * @param array $wrapperAttributes Attributes XHTML attributes for the div that 
 * wraps each displayed file. If empty or null, this will not wrap the displayed 
 * file in a div.
 * @return string HTML
 */
function file_markup($files, array $props = array(), $wrapperAttributes = array('class' => 'item-file'))
{
    if (!is_array($files)) {
        $files = array($file);
    }
    $helper = new Omeka_View_Helper_FileMarkup;
    $output = '';
    foreach ($files as $file) {
        $output .= $helper->fileMarkup($file, $props, $wrapperAttributes);
    }
    return $output;
}

/**
 * Return display for ID3 metadata for the current file.
 *
 * @param array $options Optional
 * @param File|null $file Optional
 * @return string|array
 */
function file_id3_metadata(array $options = array(), $file = null)
{
    if (!$file) {
        $file = get_current_record('file');
    }
    return get_view()->fileId3Metadata($file, $options);
}

/**
 * Returns the most recent files
 *
 * @param integer $num The maximum number of recent files to return
 * @return array
 */
function get_recent_files($num = 10)
{
    return get_records('File', array('sort_field' => 'added', 'sort_dir' => 'd'), $num);
}

/**
 * Generate attributes for XHTML tags.
 *
 * @param array|string $attributes Attributes for the tag.  If this is a
 * string, it will assign both 'name' and 'id' attributes that value for
 * the tag.
 * @param string $value
 * @return string
 */
function tag_attributes($attributes, $value=null)
{
    if (is_string($attributes)) {
        $toProcess['name'] = $attributes;
        $toProcess['id'] = $attributes;
    } else {
        //don't allow 'value' to be set specifically as an attribute (why = consistency)
        unset($attributes['value']);
        $toProcess = $attributes;
    }

    $attr = array();
    foreach ($toProcess as $key => $attribute) {
        // Only include the attribute if its value is a string.
        if (is_string($attribute)) {
            $attr[$key] = $key . '="' . html_escape( $attribute ) . '"';
        }
    }
    return join(' ',$attr);
}

/**
 * Make a simple search form for the items.
 *
 * Contains a single fieldset with a text input and submit button.
 *
 * @param string $buttonText Optional Defaults to 'Search'.
 * @param array $formProperties Optional XHTML attributes for the form.  Defaults
 * to setting id="simple-search".
 * @param string $uri Optional Action for the form.  Defaults to 'items/browse'.
 * @return string
 */
function simple_search_form($buttonText = null, $formProperties=array('id'=>'simple-search'), $uri = null)
{
    if (!$buttonText) {
        $buttonText = __('Search');
    }

    // Always post the 'items/browse' page by default (though can be overridden).
    if (!$uri) {
        $uri = apply_filters('simple_search_default_uri', url('items/browse'));
    }

    $searchQuery = array_key_exists('search', $_GET) ? $_GET['search'] : '';
    $formProperties['action'] = $uri;
    $formProperties['method'] = 'get';
    $html  = '<form ' . tag_attributes($formProperties) . '>' . "\n";
    $html .= '<fieldset>' . "\n\n";
    $html .= get_view()->formText('search', $searchQuery);
    $html .= get_view()->formSubmit('submit_search', $buttonText, array('class' => 'blue'));
    $html .= '</fieldset>' . "\n\n";

    // add hidden fields for the get parameters passed in uri
    $parsedUri = parse_url($uri);
    if (array_key_exists('query', $parsedUri)) {
        parse_str($parsedUri['query'], $getParams);
        foreach($getParams as $getParamName => $getParamValue) {
            $html .= get_view()->formHidden($getParamName, $getParamValue);
        }
    }

    $html .= '</form>';
    return $html;
}

/**
 * Return the proper HTML for a form input for a given Element record.
 *
 * Assume that the given element has access to all of its values (for example,
 * all values of a Title element for a given Item).
 *
 * This will output as many form inputs as there are values for a given
 * element.  In addition to that, it will give each set of inputs a label and
 * a span with class="tooltip" containing the description for the element.
 * This span can either be displayed, hidden with CSS or converted into a
 * tooltip with javascript.
 *
 * All sets of form inputs for elements will be wrapped in a div with
 * class="field".
 *
 * @param Element|array $element
 * @param Omeka_Record_AbstractRecord $record
 * @param array $options Optional
 * @return string HTML
 */
function element_form($element, $record, $options = array())
{
    $html = '';
    // If we have an array of Elements, loop through the form to display them.
    if (is_array($element)) {
        foreach ($element as $key => $e) {
            $html .= get_view()->elementForm($e, $record, $options);
        }
    } else {
        $html = get_view()->elementForm($element, $record, $options);
    }
    return $html;
}

/**
 * Used within the admin theme (and potentially within plugins) to display a form
 * for a record for a given element set.
 *
 * @uses element_form()
 * @param Omeka_Record_AbstractRecord $record
 * @param string $elementSetName The name of the element set or 'Item Type Metadata' for an item's item type data
 * @return string
 */
function element_set_form($record, $elementSetName)
{
    $recordType = get_class($record);
    
    if ($recordType == 'Item' && $elementSetName == 'Item Type Metadata') {
        $elements = $record->getItemTypeElements();
    } else {
        $elements = get_db()->getTable('Element')->findBySet($elementSetName);
    }
    $filterName = array('Form', $recordType, $elementSetName);
    $elements = apply_filters(
        $filterName, 
        $elements,
        array('recordType' => $recordType, 'record' => $record, 'elementSetName' => $elementSetName)
    );
            
    $html = element_form($elements, $record);

    return $html;
}

/**
 * Adds the "Select Below" or other label option to a set of select
 * options.
 *
 * @param array $options
 * @param string|null $labelOption
 * @return array
 */
function label_table_options($options, $labelOption = null)
{
    if ($labelOption === null) {
        $labelOption = __('Select Below ');
    }
    return array('' => $labelOption) + $options;
}

/**
 * Get the options array for a given table.
 *
 * @param string $tableClass
 * @param string $labelOption
 * @param array $searchParams Optional search parameters on table.
 */
function get_table_options($tableClass, $labelOption = null, $searchParams = array())
{
    $options = get_db()->getTable($tableClass)->findPairsForSelectForm($searchParams);
    $options = apply_filters(Inflector::underscore($tableClass) . '_select_options', $options);
    return label_table_options($options, $labelOption);
}

/**
 * Return the view object.  Should be used only to avoid function scope
 * issues within other theme helper functions.
 *
 * @return Omeka_View
 */
function get_view()
{
    return Zend_Registry::get('view');
}

/**
 * Output a <link> tag for the RSS feed so the browser can auto-discover the field.
 *
 * @uses items_output_url()
 * @return string HTML
 */
function auto_discovery_link_tags() {
    $html = '<link rel="alternate" type="application/rss+xml" title="'. __('Omeka RSS Feed') . '" href="'. html_escape(items_output_url('rss2')) .'" />';
    $html .= '<link rel="alternate" type="application/atom+xml" title="'. __('Omeka Atom Feed') .'" href="'. html_escape(items_output_url('atom')) .'" />';
    return $html;
}

/**
 * Includes a file from the common/ directory, passing variables into that script.
 *
 * @param string $file Filename
 * @param array $vars A keyed array of variables to be extracted into the script
 * @param string $dir Defaults to 'common'
 */
function common($file, $vars = array(), $dir = 'common')
{
    return get_view()->partial($dir . '/' . $file . '.php', $vars);
}

/**
 * Include the header script into the view
 *
 * @see common()
 * @param array Keyed array of variables
 * @param string $file Filename of header script (defaults to 'header')
 */
function head($vars = array(), $file = 'header')
{
    return common($file, $vars);
}

/**
 * Include the footer script into the view
 *
 * @param array Keyed array of variables
 * @param string $file Filename of footer script (defaults to 'footer')
 */
function foot($vars = array(), $file = 'footer') {
    return common($file, $vars);
}

/**
 * Return a flashed message from the controller
 *
 * @return string
 */
function flash()
{
    return get_view()->flash();
}

/**
 * Return the value of a particular site setting.  This can be used to display
 * any option that would be retrieved with get_option().
 *
 * Content for any specific option can be filtered by using a filter named
 * 'display_option_(option)' where (option) is the name of the option, e.g.
 * 'display_option_site_title'.
 *
 * @uses get_option()
 * @return string
 */
function option($name)
{
    $name = apply_filters("display_option_$name", get_option($name));
    $name = html_escape($name);
    return $name;
}

/**
 * Get a set of records from the database.
 *
 * @uses Omeka_Db_Table::findBy
 *
 * @param string $recordType Type of records to get.
 * @param array $params Array of search parameters for records.
 * @param integer $limit Maximum number of records to return.
 * 
 * @return array An array of result records (of $recordType).
 */
function get_records($recordType, $params = array(), $limit = 10)
{
    return get_db()->getTable($recordType)->findBy($params, $limit);
}

/**
 * Get the total number of a given type of record in the database.
 *
 * @uses Omeka_Db_Table::count
 *
 * @param string $recordType Type of record to count.
 *
 * @return integer Number of records of $recordType in the database.
 */
function total_records($recordType)
{
    return get_db()->getTable($recordType)->count();
}

/**
 * Return an iterator used for looping an array of records.
 * 
 * @uses Omeka_View_Helper_Loop
 * @param string $recordsVar
 * @param array|null $records
 * @return Omeka_Record_Iterator
 */
function loop($recordsVar, $records = null)
{
    return get_view()->loop($recordsVar, $records);
}

/**
 * Set records to the view for iteration.
 * 
 * @param string $recordsVar
 * @param array $records
 */
function set_loop_records($recordsVar, array $records)
{
    get_view()->setLoopRecords($recordsVar, $records);
}

/**
 * Get records from the view for iteration.
 * 
 * @param string $recordsVar
 * @return array|null
 */
function get_loop_records($recordsVar, $throwException = true)
{
    return get_view()->getLoopRecords($recordsVar, $throwException);
}

/**
 * Check if records have been set to the view for iteration.
 * 
 * @param string $recordsVar
 * @return bool
 */
function has_loop_records($recordsVar)
{
    return (bool) get_view()->getLoopRecords($recordsVar, false);
}

/**
 * Set a record to the view as the current record.
 * 
 * @uses Omeka_View_Helper_SetCurrentRecord
 * @param string $recordVar
 * @param Omeka_Record_AbstractRecord $record
 * @param bool $setPreviousRecord
 */
function set_current_record($recordVar, Omeka_Record_AbstractRecord $record, $setPreviousRecord = false)
{
    get_view()->setCurrentRecord($recordVar, $record, $setPreviousRecord);
}

/**
 * Get the current record from the view.
 * 
 * @uses Omeka_View_Helper_GetCurrentRecord
 * @param string $recordVar
 * @param bool $throwException
 * @return Omeka_Record_AbstractRecord|false
 */
function get_current_record($recordVar, $throwException = true)
{
    return get_view()->getCurrentRecord($recordVar, $throwException);
}

/**
 * Get a record by its ID.
 * 
 * @param string $recordVar
 * @param int $recordId
 * @return Omeka_Record_AbstractRecord|null
 */
function get_record_by_id($recordVar, $recordId)
{
    return get_db()->getTable(Inflector::camelize($recordVar))->find($recordId);
}

/**
 * Get all output formats available in the current action.
 *
 * @return array A sorted list of contexts.
 */
function get_current_action_contexts()
{
    $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
    $contexts = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getActionContexts($actionName);
    sort($contexts);
    return $contexts;
}

/**
 * Builds an HTML list containing all available output format contexts for the
 * current action.
 *
 * @param bool True = unordered list; False = use delimiter
 * @param string If the first argument is false, use this as a delimiter.
 * @return string HTML
 */
function output_format_list($list = true, $delimiter = ' | ')
{
    $actionContexts = get_current_action_contexts();
    $html = '';

    // Do not display the list if there are no output formats available in the
    // current action.
    if (empty($actionContexts)) {
        return false;
    }

    // Unordered list format.
    if ($list) {
        $html .= '<ul id="output-format-list">';
        foreach ($actionContexts as $key => $actionContext) {
            $query = $_GET;
            $query['output'] = $actionContext;
            $html .= '<li><a href="' . html_escape(url() . '?' . http_build_query($query)) . '">' . $actionContext . '</a></li>';
        }
        $html .= '</ul>';

    // Delimited format.
    } else {
        $html .= '<p id="output-format-list">';
        foreach ($actionContexts as $key => $actionContext) {
            $query = $_GET;
            $query['output'] = $actionContext;
            $html .= '<a href="' . html_escape(url() . '?' . http_build_query($query)) . '">' . $actionContext . '</a>';
            $html .= (count($actionContexts) - 1) == $key ? '' : $delimiter;
        }
        $html .= '</p>';
    }

    return $html;
}

function browse_headings($headings)
{
    $sortParam = Omeka_Db_Table::SORT_PARAM;
    $sortDirParam = Omeka_Db_Table::SORT_DIR_PARAM;
    $req = Zend_Controller_Front::getInstance()->getRequest();
    $currentSort = trim($req->getParam($sortParam));
    $currentDir = trim($req->getParam($sortDirParam));

    foreach ($headings as $label => $column) {
        if($column) {
            $urlParams = $_GET;
            $urlParams[$sortParam] = $column;
            $class = '';
            if ($currentSort && $currentSort == $column) {
                if ($currentDir && $currentDir == 'd') {
                    $class = 'class="sorting desc"';
                    $urlParams[$sortDirParam] = 'a';
                } else {
                    $class = 'class="sorting asc"';
                    $urlParams[$sortDirParam] = 'd';
                }
            }
            $url = url(array(), null, $urlParams);
            echo "<th $class scope=\"col\"><a href=\"$url\">$label</a></th>";
        } else {
            echo "<th scope=\"col\">$label</th>";
        }
    }
}

/**
 * Returns a <body> tag with attributes. Attributes
 * can be filtered using the 'body_tag_attributes' filter.
 *
 * @uses tag_attributes()
 * @return string An HTML <body> tag with attributes and their values.
 */
function body_tag($attributes = array())
{
    $attributes = apply_filters('body_tag_attributes', $attributes);
    if ($attributes = tag_attributes($attributes)) {
        return "<body ". $attributes . ">\n";
    }
    return "<body>\n";
}

/**
 * Return a list of the current search filters in use.
 *
 * @params array $params Optional params to replace the ones read from the request.
 */
function search_filters(array $params = null)
{
    return get_view()->searchFilters($params);
}

/**
 * Get a piece or pieces of metadata for a record.
 *
 * @see Omeka_View_Helper_Metadata
 * @param Omeka_Record_AbstractRecord|string $record The record to get metadata
 *  for. If an Omeka_Record_AbstractRecord, that record is used. If a string,
 *  that string is used to look up a record in the current view.
 * @param mixed $metadata The metadata to get. If an array is given, this is
 *  Element metadata, identified by array('Element Set', 'Element'). If a string,
 *  the metadata is a record-specific "property."
 * @param array $options Options for getting the metadata.
 * @return mixed
 */
function metadata($record, $metadata, $options = array())
{
    return get_view()->metadata($record, $metadata, $options);
}

/**
 * Return the set of all element text metadata for a record.
 *
 * @uses Omeka_View_Helper_AllElementTexts
 * 
 * @param Omeka_Record_AbstractRecord|string $record The record to get the
 *  element text metadata for.
 * @param array $options Options for getting the metadata.
 * @return string|array
 */
function all_element_texts($record, $options = array())
{
    return get_view()->allElementTexts($record, $options);
}

/**
 * @uses file_markup()
 * @uses get_current_record()
 * @param array $options
 * @param array $wrapperAttributes
 * @param Item|null $item Check for this specific item record (current item if null).
 * @return string HTML
 */
function files_for_item($options = array(), $wrapperAttributes = array('class'=>'item-file'), $item = null)
{
    if (!$item) {
        $item = get_current_record('item');
    }

    return file_markup($item->Files, $options, $wrapperAttributes);
}

/**
 * Returns the HTML markup for displaying a random featured item.  Most commonly
 * used on the home page of public themes.
 *
 * @param boolean $withImage Whether or not the featured item should have an image associated
 * with it.  If set to true, this will either display a clickable square thumbnail
 * for an item, or it will display "You have no featured items." if there are
 * none with images.
 * @return string HTML
 */
function random_featured_item($withImage = null)
{
    $html = '<h2>'. __('Featured Item') .'</h2>';
    $html .= random_featured_items('1', $withImage);
    return $html;
}

/**
 * Return the next item in the database.
 *
 * @todo Should this look for the next item in the loop, or just via the database?
 * @param Item|null Check for this specific item record (current item if null).
 * @return Item|null
 */
function get_next_item($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    return $item->next();
}

/**
 * @see get_previous_item()
 * @param Item|null Check for this specific item record (current item if null).
 * @return Item|null
 */
function get_previous_item($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    return $item->previous();
}

/**
 * Return a valid citation for the current item.
 *
 * Generally follows Chicago Manual of Style note format for webpages.  Does not
 * account for multiple creators or titles.
 *
 * @param Item|null Check for this specific item record (current item if null).
 * @return string
 */
function item_citation($item = null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    
    $citation = '';
    
    $creators = metadata($item, array('Dublin Core', 'Creator'), array('all' => true));
    // Strip formatting and remove empty creator elements.
    $creators = array_filter(array_map('strip_formatting', $creators));
    if ($creators) {
        switch (count($creators)) {
            case 1:
                $creator = $creators[0];
                break;
            case 2:
                $creator = "{$creators[0]} and {$creators[1]}";
                break;
            case 3:
                $creator = "{$creators[0]}, {$creators[1]}, and {$creators[2]}";
                break;
            default:
                $creator = "{$creators[0]} et al.";
        }
        $citation .= "$creator, ";
    }
    
    $title = strip_formatting(metadata($item, array('Dublin Core', 'Title')));
    if ($title) {
        $citation .= "&#8220;$title,&#8221; ";
    }
    
    $siteTitle = strip_formatting(option('site_title'));
    if ($siteTitle) {
        $citation .= "<em>$siteTitle</em>, ";
    }
    
    $accessed = date('F j, Y');
    $url = html_escape(record_url($item, null, true));
    $citation .= "accessed $accessed, $url.";
    
    return apply_filters('item_citation', $citation, array('item' => $item));
}

/**
 * Determine whether or not a specific element uses HTML.  By default this will
 * test the first element text, though it is possible to test against a different
 * element text by modifying the $index parameter.
 *
 * @param string
 * @param string
 * @param integer
 * @param Item|null Check for this specific item record (current item if null).
 * @return boolean
 */
function item_field_uses_html($elementSetName, $elementName, $index=0, $item = null)
{
    if (!$item) {
        $item = get_current_record('item');
    }

    $textRecords = $item->getElementTexts($elementSetName, $elementName);
    $textRecord = @$textRecords[$index];

    return ($textRecord instanceof ElementText and $textRecord->isHtml());
}

/**
 * @see item_thumbnail()
 * @param array $props
 * @param integer $index
 * @return string HTML
 */
function item_fullsize($props = array(), $index = 0, $item = null)
{
    return item_image('fullsize', $props, $index, $item);
}

/**
 * Determine whether or not the item has any files associated with it.
 *
 * @see has_files()
 * @uses Item::hasFiles()
 * @param Item|null Check for this specific item record (current item if null).
 * @return boolean
 */
function item_has_files($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    return $item->hasFiles();
}

/**
 * @param Item|null Check for this specific item record (current item if null).
 * @return boolean
 */
function item_has_tags($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    return (count($item->Tags) > 0);
}

/**
 * Determine whether or not the item has a thumbnail image that it can display.
 *
 * @param Item|null Check for this specific item record (current item if null).
 */
function item_has_thumbnail($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    return $item->hasThumbnail();
}

/**
 * Primarily used internally by other theme helpers, not intended to be used
 * within themes.  Plugin writers creating new helpers may want to use this
 * function to display a customized derivative image.
 *
 * @param string $imageType
 * @param array $props
 * @param integer $index
 * @param Item|null Check for this specific item record (current item if null).
 */
function item_image($imageType, $props = array(), $index = 0, $item = null)
{
    if (!$item) {
        $item = get_current_record('item');
    }

    $imageFile = get_db()->getTable('File')->findWithImages($item->id, $index);

    $media = new Omeka_View_Helper_FileMarkup;
    return $media->image_tag($imageFile, $props, $imageType);
}

/**
 * Returns the HTML for an item search form
 *
 * @param array $props
 * @param string $formActionUri
 * @return string
 */
function items_search_form($props=array(), $formActionUri = null)
{
    return get_view()->partial('items/advanced-search-form.php', array('formAttributes'=>$props, 'formActionUri'=>$formActionUri));
}

/**
 * @see item_thumbnail()
 * @param array $props
 * @param integer $index
 * @param Item $item The item to which the image belongs
 * @return string HTML
 */
function item_square_thumbnail($props = array(), $index = 0, $item = null)
{
    return item_image('square_thumbnail', $props, $index, $item);
}

/**
 * HTML for a thumbnail image associated with an item.  Default parameters will
 * use the first image, but that can be changed by modifying $index.
 *
 * @uses item_image()
 * @param array $props A set of attributes for the <img /> tag.
 * @param integer $index The position of the file to use (starting with 0 for
 * the first file).
 * @param Item $item The item to which the image belongs
 * @return string HTML
 */
function item_thumbnail($props = array(), $index = 0, $item = null)
{
    return item_image('thumbnail', $props, $index, $item);
}

/**
 * Returns the most recent items
 *
 * @param integer $num The maximum number of recent items to return
 * @return array
 */
function get_recent_items($num = 10)
{
    return get_db()->getTable('Item')->findBy(array('sort_field' => 'added', 'sort_dir' => 'd'), $num);
}

/**
 * Returns a random featured item
 *
 * @param boolean|null $hasImage
 * @return Item
 */
function get_random_featured_item($hasImage=null)
{
    $item = get_random_featured_items('1', $hasImage);
    return $item[0];
}

/**
 * Returns multiple random featured item
 *
 * @param integer $num The maximum number of recent items to return
 * @param boolean|null $hasImage
 * @return array $items
 */
function get_random_featured_items($num = 5, $hasImage = null)
{
    return get_records('Item', array('featured'=>1, 'sort_field' => 'random', 'hasImage' => $hasImage), $num);
}

function random_featured_items($num = 5, $hasImage = null)
{
    $html = '';

    if ($randomFeaturedItems = get_random_featured_items($num, $hasImage)) {
        foreach ($randomFeaturedItems as $randomItem) {
            $itemTitle = metadata($randomItem, array('Dublin Core', 'Title'));

            $html .= '<h3>' . link_to_item($itemTitle, array(), 'show', $randomItem) . '</h3>';

            if (item_has_thumbnail($randomItem)) {
                $html .= link_to_item(item_square_thumbnail(array(), 0, $randomItem), array('class'=>'image'), 'show', $randomItem);
            }

            if ($itemDescription = metadata($randomItem, array('Dublin Core', 'Description'), array('snippet'=>150))) {
                $html .= '<p class="item-description">' . $itemDescription . '</p>';
            }
        }
    } else {
        $html .= '<p>'.__('No featured items are available.').'</p>';
    }

    return $html;
}

/**
 * Return the set of values for item type elements.
 * @param Item|null Check for this specific item record (current item if null).
 * @return array
 */
function item_type_elements($item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }
    $elements = $item->getItemTypeElements();
    foreach ($elements as $element) {
        $elementText[$element->name] = metadata($item, array(ELEMENT_SET_ITEM_TYPE, $element->name));
    }
    return $elementText;
}

/**
 * Uses url() to generate <a> tags for a given link.
 *
 * @param Omeka_Record_AbstractRecord|string $record The name of the controller 
 * to use for the link.  If a record instance is passed, then it inflects the 
 * name of the controller from the record class.
 * @param string $action The action to use for the link (optional)
 * @param string $text The text to put in the link.  Default is 'View'.
 * @param array $props Attributes for the <a> tag
 * @param array $queryParams the parameters in the uri query
 * @return string HTML
 */
function link_to($record, $action=null, $text=null, $props = array(), $queryParams=array())
{
    // If we're linking directly to a record, use the URI for that record.
    if($record instanceof Omeka_Record_AbstractRecord) {
        $url = record_url($record, $action);
    }
    else {
        // Otherwise $record is the name of the controller to link to.
        $urlOptions = array();
        //Use Zend Framework's built-in 'default' route
        $route = 'default';
        $urlOptions['controller'] = (string) $record;
        if($action) $urlOptions['action'] = (string) $action;
        $url = url($urlOptions, $route, $queryParams, true);
    }

    if ($text === null) {
        $text = __('View');
    }

    $attr = !empty($props) ? ' ' . tag_attributes($props) : '';

    return '<a href="'. html_escape($url) . '"' . $attr . '>' . $text . '</a>';
}

/**
 * Return HTML for a link to the advanced search form.
 *
 * @param string $text Optional Text of the link. Default is 'Advanced Search'.
 * @param array $props Optional XHTML attributes for the link.
 * @param string $uri Optional Action for the form.  Defaults to 'items/browse'.
 * @return string
 */
function link_to_item_search($text = null, $props = array(), $uri=null)
{
    if (!$text) {
        $text = __('Advanced Search');
    }

    if (!$uri) {
        $uri = apply_filters('advanced_search_link_default_uri', url('items/advanced-search'));
    }
    // Is appending the query string directly a security issue?  We should figure that out.
    $props['href'] = $uri . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
    return '<a ' . tag_attributes($props) . '>' . $text . '</a>';
}

/**
 * Get the proper HTML for a link to the browse page for items, with any appropriate
 * filtering parameters passed to the URL.
 *
 * @param string $text Text to display in the link.
 * @param array $browseParams Optional Any parameters to use to build the browse page URL, e.g.
 * array('collection' => 1) would build items/browse?collection=1 as the URL.
 * @param array $linkProperties Optional XHTML attributes for the link.
 * @return string HTML
 */
function link_to_items_browse($text, $browseParams = array(), $linkProperties = array())
{
    return link_to('items', 'browse', $text, $linkProperties, $browseParams);
}

/**
 * Link to the collection that the current item belongs to.
 *
 * The default text displayed for this link will be the name of the collection,
 * but that can be changed by passing a string argument.
 *
 * @param string|null $text Optional Text for the link.
 * @param array $props Optional XHTML attributes for the <a> tag.
 * @param string $action Optional 'show' by default.
 * @return string
 */
function link_to_collection_for_item($text = null, $props = array(), $action = 'show')
{
    if ($collection = get_collection_for_item()) {
        return link_to_collection($text, $props, $action, $collection);
    }

    return __('No Collection');
}

function link_to_items_in_collection($text = null, $props = array(), $action = 'browse', $collectionObj = null)
{
    if (!$collectionObj) {
        $collectionObj = get_current_record('collection');
    }

    $queryParams = array();
    $queryParams['collection'] = $collectionObj->id;

    if ($text === null) {
        $text = $collectionObj->totalItems();
    }

    return link_to('items', $action, $text, $props, $queryParams);
}

function link_to_items_with_item_type($text = null, $props = array(), $action = 'browse', $itemTypeObj = null)
{
    if (!$itemTypeObj) {
        $itemTypeObj = get_current_record('item_type');
    }

    $queryParams = array();
    $queryParams['type'] = $itemTypeObj->id;

    if ($text === null) {
        $text = $itemTypeObj->totalItems();
    }

    return link_to('items', $action, $text, $props, $queryParams);
}

/**
 * Return the HTML for a link to the file metadata page for a particular file.
 *
 * If no File object is specified, this will determine the file to use through
 * context.
 *
 * The text of the link defaults to the DC:Title of the file record, then to
 * the original filename, unless otherwise specified.
 *
 * @param array
 * @param string
 * @param File
 * @return string
 */
function link_to_file_show($attributes = array(), $text = null, $file = null)
{
    if (!$file) {
        $file = get_current_record('file');
    }

    if (!$text) {
        $fileTitle = strip_formatting(metadata($file, array('Dublin Core', 'Title')));
        $text = $fileTitle ? $fileTitle : metadata($file, 'Original Filename');
    }

    return link_to($file, 'show', $text, $attributes);
}

/**
 * @param string HTML for the text of the link.
 * @param array Properties for the <a> tag. (optional)
 * @param string The page to link to (this will be the 'show' page almost always
 * within the public theme).
 * @param Item Used for dependency injection testing or to use this function outside
 * the context of a loop.
 * @return string HTML
 */
function link_to_item($text = null, $props = array(), $action = 'show', $item=null)
{
    if (!$item) {
        $item = get_current_record('item');
    }

    $text = (!empty($text) ? $text : strip_formatting(metadata($item, array('Dublin Core', 'Title'))));

    return link_to($item, $action, $text, $props);
}

/**
 * @param string $text The text of the link.
 * @param array $params A set of query string parameters to merge in to the href
 * of the link.  E.g., if this link was clicked on the items/browse?collection=1
 * page, and array('foo'=>'bar') was passed as this argument, the new URI would be
 * items/browse?collection=1&foo=bar.
 */
function link_to_items_rss($text = null, $params=array())
{
    if (!$text) {
        $text = __('RSS');
    }
    return '<a href="' . html_escape(items_output_url('rss2', $params)) . '" class="rss">' . $text . '</a>';
}

/**
 * Link to the item immediately following the current one.
 *
 * @uses link_to()
 * @return string
 */
function link_to_next_item_show($text=null, $props=array())
{
    if (!$text) {
        $text = __("Next Item &rarr;");
    }

    $item = get_current_record('item');
    if($next = $item->next()) {
        return link_to($next, 'show', $text, $props);
    }
}

/**
 * @see link_to_next_item_show()
 * @return string
 */
function link_to_previous_item_show($text=null, $props=array())
{
    if (!$text) {
        $text = __('&larr; Previous Item');
    }

    $item = get_current_record('item');
    if($previous = $item->previous()) {
        return link_to($previous, 'show', $text, $props);
    }
}

/**
 * @param string $text Optional text to use for the title of the collection.  Default
 * behavior is to use the name of the collection.
 * @param array $props Set of attributes to use for the link.
 * @param array $action The action to link to for the collection.  Default is 'show'.
 * @param array $collectionObj Optional Collection record can be passed to this
 * to override the collection object retrieved by get_current_record().
 * @return string
 */
function link_to_collection($text=null, $props=array(), $action='show', $collectionObj = null)
{
    if (!$collectionObj) {
        $collectionObj = get_current_record('collection');
    }

    $collectionName = metadata($collectionObj, 'name');

    $text = (!empty($text) ? $text : (!empty($collectionName) ? $collectionName : __('[Untitled]')));

    return link_to($collectionObj, $action, $text, $props);
}

/**
 * @return string
 */
function link_to_home_page($text = null, $props = array())
{
    if (!$text) {
        $text = option('site_title');
    }
    $uri = WEB_ROOT;
    return '<a href="' . html_escape($uri) . '" '.tag_attributes($props).'>' . $text . "</a>\n";
}

/**
 * @see link_to_home_page()
 * @return string
 */
function link_to_admin_home_page($text = null, $props = array())
{
    if (!$text) {
        $text = option('site_title');
    }
    return '<a href="' . html_escape(admin_url('')) . '" ' . tag_attributes($props)
         . '>' . $text . "</a>\n";
}

/**
 * Generate an unordered list of navigation links (and subnavigation links),
 * with class "current" for any links corresponding to the current page
 *
 * For example:
 * <code>nav(array('Themes' => url('themes/browse')));</code>
 * generates
 * <code><li class="nav-themes"><a href="themes/browse">Themes</a></li></code>
 *
 * @uses is_current_url()
 * @param array A keyed array, where key = text of the link, and value = uri of the link,
 * or value = another ordered array $a with the following recursive structure:
 * $a['uri'] = URI of the link
 * $a['subnav_links'] = array of $sublinks for the sub navigation (this can be recursively structured like $links)
 * $a['subnav_attributes'] = associative array of attributes for the sub navigation
 *
 * For example:
 * $links = array('Browse' => 'http://yoursite.com/browse',
 *                'Categories' => array('uri' => 'http://yoursite.com/categories',
 *                                      'subnav_links' => array('Dogs' => 'http://yoursite.com/dogs',
 *                                                              'Cats' => 'http://yoursite.com/cats'),
 *                                      'subnav_attributes' => array('class' => 'subnav')),
 *                'Contact Us' => 'http://yoursite.com/contact-us');
 * echo nav($links);
 *
 * This would produce:
 * <li><a href="http://yoursite.com/browse">Browse</a></li>
 * <li><a href="http://yoursite.com/categories">Categories</a>
 *     <ul class="subnav">
 *        <li><a href="http://yoursite.com/dogs">Dogs</a></li>
 *        <li><a href="http://yoursite.com/cats">Cats</a></li>
 *    </ul>
 * </li>
 * <li><a href="http://yoursite.com/contact-us">Contact Us</a><li>
 *
 * @param integer|null $maxDepth The maximum number of sub navigation levels to display.
 * By default it is 0, which means it will only display the top level of links.
 * If null, it will display all the levels.
 *
 * @return string HTML for the unordered list
 */
function nav(array $links, $maxDepth = 0)
{
    // Get the current uri from the request
    $current = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();

    $nav = '';
    foreach( $links as $text => $uri ) {

        // Get the subnavigation attributes and links
        $subNavLinks = null;
        if (is_array($uri)) {
            $subNavLinks = $uri['subnav_links'];
            if (!is_array($subNavLinks)) {
                $subNavLinks = array();
            }
            $subNavAttributes = $uri['subnav_attributes'];
            if (!is_array($subNavAttributes)) {
                $subNavAttributes = array();
            }
            $uri = (string) $uri['uri'];
        }

        // Build a link if the uri is available, otherwise simply list the text without a hyperlink
        $nav .= '<li class="' . text_to_id($text, 'nav');
        if ($uri == '') {
            $nav .= '">' . html_escape($text);
        } else {
            // If the uri is the current uri, then give it the 'current' class
            $nav .= (is_current_url($uri) ? ' current':'') . '">' . '<a href="' . html_escape($uri) . '">' . html_escape($text) . '</a>';
        }

        // Display the subnavigation links if they exist and if the max depth has not been reached
        if ($subNavLinks !== null && ($maxDepth === null || $maxDepth > 0)) {
            $subNavAttributes = !empty($subNavAttributes) ? ' ' . tag_attributes($subNavAttributes) : '';
            $nav .= "\n" . '<ul' . $subNavAttributes . '>' . "\n";
            if ($maxDepth === null) {
                $nav .= nav($subNavLinks, null);
            } else {
                $nav .= nav($subNavLinks, $maxDepth - 1);
            }
            $nav .= '</ul>' . "\n";
        }

        $nav .= '</li>' . "\n";
    }

    return $nav;
}

/**
 * Return HTML for the set of pagination links.
 *
 * @param array $options Optional Configurable parameters for the pagination
 * links.  The following options are available:
 *      'scrolling_style' (string) See Zend_View_Helper_PaginationControl
  * for more details.  Default 'Sliding'.
 *      'partial_file' (string) View script to use to render the pagination HTML.
 * Default is 'common/pagination_control.php'.
 *      'page_range' (integer) See Zend_Paginator::setPageRange() for details.
 * Default is 5.
 *      'total_results' (integer) Total results to paginate through.  Default is
 * provided by the 'total_results' key of the 'pagination' array that is typically
 * registered by the controller.
 *      'page' (integer) Current page of the result set.  Default is the 'page'
 * key of the 'pagination' array.
 *      'per_page' (integer) Number of results to display per page.  Default is
 * the 'per_page' key of the 'pagination' array.
 * @return string HTML for the pagination links.
 */
function pagination_links($options = array())
{
    if (Zend_Registry::isRegistered('pagination')) {
        // If the pagination variables are registered, set them for local use.
        $p = Zend_Registry::get('pagination');
    } else {
        // If the pagination variables are not registered, set required defaults
        // arbitrarily to avoid errors.
        $p = array('total_results'   => 1,
                   'page'            => 1,
                   'per_page'        => 1);
    }

    // Set preferred settings.
    $scrollingStyle   = isset($options['scrolling_style']) ? $options['scrolling_style']     : 'Sliding';
    $partial          = isset($options['partial_file'])    ? $options['partial_file']        : 'common/pagination_control.php';
    $pageRange        = isset($options['page_range'])      ? (int) $options['page_range']    : 5;
    $totalCount       = isset($options['total_results'])   ? (int) $options['total_results'] : (int) $p['total_results'];
    $pageNumber       = isset($options['page'])            ? (int) $options['page']          : (int) $p['page'];
    $itemCountPerPage = isset($options['per_page'])        ? (int) $options['per_page']      : (int) $p['per_page'];

    // Create an instance of Zend_Paginator.
    $paginator = Zend_Paginator::factory($totalCount);

    // Configure the instance.
    $paginator->setCurrentPageNumber($pageNumber)
              ->setItemCountPerPage($itemCountPerPage)
              ->setPageRange($pageRange);

    return get_view()->paginationControl($paginator,
                                    $scrollingStyle,
                                    $partial);
}

/**
 * Helper function to be used in public themes to allow plugins to modify the navigation of those themes.
 *
 * Plugins can modify navigation by adding filters to specific subsets of the
 *  navigation. For instance, most themes will have what might be called a 'main'
 *  navigation set on the header of the site. This main navigation header would
 *  be attached to a filter called 'public_navigation_main', which would always
 *  act on that particular navigation. You would signal to the plugins to
 *  differentiate between the different navigation elements by passing the 2nd
 *  argument as 'main', so that it knew that this was the main navigation.
 *
 * @see apply_filters()
 * @param array $navArray
 * @param string|null $navType
 * @param integer|null $maxDepth
 * @return string HTML
 */
function public_nav(array $navArray, $navType=null, $maxDepth = 0)
{
    if ($navType) {
        $filterName = 'public_navigation_' . $navType;
        $navArray = apply_filters($filterName, $navArray);
    }
    return nav($navArray, $maxDepth);
}

/**
 * Output the main navigation for the public side
 *
 * @return Zend_View_Helper_Navigation_Menu Can be echoed like a string or
 *  manipulated by the theme.
 */
function public_nav_main()
{
    $view = get_view();
    $nav = new Omeka_Navigation;
    $nav->loadAsOption(Omeka_Navigation::PUBLIC_NAVIGATION_MAIN_OPTION_NAME);
    return $view->navigation()->menu($nav);
}

/**
 * Alias for public_nav($array, 'items'). Provides a navigation and filter for
 * the items/browse page.
 * 
 * @param array $navArray
 * @param integer|null $maxDepth
 * @uses public_nav()
 * @return string
 */
function public_nav_items(array $navArray = null, $maxDepth = 0)
{
    if (!$navArray) {
        $navArray = array(__('Browse All') => url('items'), __('Browse by Tag') => url('items/tags'));
    }

    return public_nav($navArray, 'items', $maxDepth);
}

/**
 * Escape the value to display properly as HTML.
 *
 * This uses the 'html_escape' filter for escaping.
 *
 * @param string
 * @return string
 */
function html_escape($value)
{
    return apply_filters('html_escape', $value);
}

/**
 * Escape the value for use in javascript.
 *
 * This is a convenience function for encoding a value using JSON notation.
 * Must be used when interpolating PHP output in javascript.
 *
 * Note on usage: do not wrap the resulting output of this function in quotes,
 * as proper JSON encoding will take care of that.
 */
function js_escape($value)
{
    return Zend_Json::encode($value);
}

/**
 * Escape the value for use in XML.
 *
 * @param string $value
 * @return string
 */
function xml_escape($value)
{
    return htmlspecialchars(preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#', '',
        $value), ENT_QUOTES);
}

/**
 * Replace new lines in a block of text with paragraph tags.
 *
 * Looks for 2 consecutive line breaks resembling a paragraph break and wraps
 * each of the paragraphs with a <p> tag.  If no paragraphs are found, then the
 * original text will be wrapped with line breaks.
 *
 * @link http://us.php.net/manual/en/function.nl2br.php#73479
 * @param string $str
 * @return string
 */
function text_to_paragraphs($str)
{
  return str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        . '</p>');
}

/**
 * Return a substring of a given piece of text.
 *
 * Note: this will only split strings on the space character.
 * this will also strip html tags from the text before getting a snippet
 *
 * @param string $text Text to take snippet of
 * @param int $startPos Starting position of snippet in string
 * @param int $endPos Maximum length of snippet
 * @param string $append String to append to snippet if truncated
 * @return string Snippet of given text
 */
function snippet($text, $startPos, $endPos, $append = '…')
{
    // strip html tags from the text
    $text = strip_formatting($text);

    $textLength = strlen($text);

    // Calculate the start position. Set to zero if the start position is
    // null or 0, OR if the start offset is greater than the length of the
    // original text.
    $startPosOffset = $startPos - $textLength;
    $startPos = !$startPos || $startPosOffset > $textLength
                ? 0
                : strrpos($text, ' ', $startPosOffset);

    // Calculate the end position. Set to the length of the text if the
    // end position is greater than or equal to the length of the original
    // text, OR if the end offset is greater than the length of the
    // original text.
    $endPosOffset = $endPos - $textLength;
    $endPos = $endPos >= $textLength || $endPosOffset > $textLength
              ? $textLength
              : strrpos($text, ' ', $endPosOffset);

    // Set the snippet by getting its substring.
    $snippet = substr($text, $startPos, $endPos - $startPos);

    // Return the snippet without the append string if the text's original
    // length equals to 1) the length of the snippet, i.e. when the return
    // string is identical to the passed string; OR 2) the calculated
    // end position, i.e. when the return string ends at the same point as
    // the passed string.
    return strlen($snippet) == $textLength || $endPos == $textLength
            ? $snippet
            : $snippet . $append;
}

/**
 * Return a substring of the text by limiting the word count.
 * Note: it strips the HTML tags from the text before getting the snippet
 *
 * @param string $text
 * @param integer $maxWords
 * @param string $ellipsis Optional '...' by default.
 * @return string
 */
function snippet_by_word_count($text, $maxWords = 20, $ellipsis = '...')
{
    // strip html tags from the text
    $text = strip_formatting($text);

    if ($maxWords > 0) {
        $textArray = explode(' ', $text);
        if (count($textArray) > $maxWords) {
            $text = implode(' ', array_slice($textArray, 0, $maxWords)) . $ellipsis;
        }
    } else {
        return '';
    }
    return $text;
}

/**
 * Strip HTML formatting (i.e. tags) from the provided string.
 *
 * This is essentially a wrapper around PHP's strip_tags() function, with the
 * added benefit of returning a fallback string in case the resulting stripped
 * string is empty or contains only whitespace.
 *
 * @uses strip_tags()
 * @param string $str The string to be stripped of HTML formatting.
 * @param string $allowableTags The string of tags to allow when stripping tags.
 * @param string $fallbackStr The string to be used as a fallback.
 * @return The stripped string.
 */
function strip_formatting($str, $allowableTags = '', $fallbackStr = '')
{
    // Strip the tags.
    $str = strip_tags($str, $allowableTags);
    // Remove non-breaking space html entities.
    $str = str_replace('&nbsp;', '', $str);
    // If only whitepace remains, return the fallback string.
    if (preg_match('/^\s*$/', $str)) {
        return $fallbackStr;
    }
    // Return the deformatted string.
    return $str;
}

/**
 * Converts a word or phrase to dashed format, i.e. Foo Bar => foo-bar
 *
 * This is primarily for easy creation of HTML ids within Omeka
 *
 * 1) convert to lowercase
 * 2) Replace whitespace with -,
 * 3) remove all non-alphanumerics,
 * 4) remove leading/trailing delimiters
 * 5) optionally prepend a piece of text
 *
 * @param string $text The text to convert
 * @param string $prepend Another string to prepend to the ID
 * @param string $delimiter The delimiter to use (- by default)
 * @return string
 */
function text_to_id($text, $prepend=null, $delimiter='-')
{
    $text = strtolower($text);
    $id = preg_replace('/\s/', $delimiter, $text);
    $id = preg_replace('/[^\w\-]/', '', $id);
    $id = trim($id, $delimiter);
    $prepend = (string) $prepend;
    return !empty($prepend) ? join($delimiter, array($prepend, $id)) : $id;
}

/**
 * Converts any URLs in a given string to links.
 *
 * @param string $str The string to be searched for URLs to convert to links.
 * @return string
 */
function url_to_link($str)
{
    $pattern = "/(\bhttps?:\/\/\S+\b)/e";
    $replace = '"<a href=\"".htmlspecialchars("$1")."\">$1</a>"';
    $str = preg_replace($pattern, $replace, $str);
    return $str;
}

/**
 * Returns the most recent tags.
 *
 * @param integer $limit The maximum number of recent tags to return
 * @return array
 */
function get_recent_tags($limit = 10)
{
    return get_records('Tag', array('sort_field' => 'time', 'sort_dir' => 'd'), $limit);
}

/**
 * Create a tag cloud made of divs that follow the hTagcloud microformat
 *
 * @param Omeka_Record_AbstractRecord|array $recordOrTags The record to retrieve 
 * tags from, or the actual array of tags
 * @param string|null The URI to use in the link for each tag.  If none given,
 *      tags in the cloud will not be given links.
 * @return string HTML for the tag cloud
 */
function tag_cloud($recordOrTags = null, $link = null, $maxClasses = 9, $tagNumber = false, $tagNumberOrder = null)
{
    if (!$recordOrTags) {
        $tags = array();
    } else if (is_string($recordOrTags)) {
        $tags = get_current_record($recordOrTags)->Tags;
    } else if ($recordOrTags instanceof Omeka_Record_AbstractRecord) {
        $tags = $recordOrTags->Tags;
    } else {
        $tags = $recordOrTags;
    }

    if (empty($tags)) {
        return '<p>' . __('No tags are available.') . '</p>';
    }

    //Get the largest value in the tags array
    $largest = 0;
    foreach ($tags as $tag) {
        if($tag["tagCount"] > $largest) {
            $largest = $tag['tagCount'];
        }
    }
    $html = '<div class="hTagcloud">';
    $html .= '<ul class="popularity">';

    if ($largest < $maxClasses) {
        $maxClasses = $largest;
    }

    foreach( $tags as $tag ) {
        $size = (int)(($tag['tagCount'] * $maxClasses) / $largest - 1);
        $class = str_repeat('v', $size) . ($size ? '-' : '') . 'popular';
        $html .= '<li class="' . $class . '">';
        if ($link) {
            $html .= '<a href="' . html_escape(url($link, array('tags' => $tag['name']))) . '">';
        }
        if($tagNumber && $tagNumberOrder == 'before') {
            $html .= ' <span class="count">'.$tag['tagCount'].'</span> ';
        }
        $html .= html_escape($tag['name']);
        if($tagNumber && $tagNumberOrder == 'after') {
            $html .= ' <span class="count">'.$tag['tagCount'].'</span> ';
        }
        if ($link) {
            $html .= '</a>';
        }
        $html .= '</li>' . "\n";
    }
    $html .= '</ul></div>';

    return $html;
}

/**
 * Output a tag string given an Item, Exhibit, or a set of tags.
 *
 * @internal Any record that has the Taggable module can be passed to this function
 * @param Omeka_Record_AbstractRecord|array $recordOrTags The record to retrieve 
 * tags from, or the actual array of tags
 * @param string|null $link The URL to use for links to the tags (if null, tags aren't linked)
 * @param string $delimiter ', ' (comma and whitespace) by default
 * @return string HTML
 */
function tag_string($recordOrTags = null, $link = 'items/browse', $delimiter = null)
{
    // Set the tag_delimiter option if no delimiter was passed.
    if (is_null($delimiter)) {
        $delimiter = get_option('tag_delimiter') . ' ';
    }

    if (!$recordOrTags) {
        $tags = array();
    } else if (is_string($recordOrTags)) {
        $tags = get_current_record($recordOrTags)->Tags;
    } else if ($recordOrTags instanceof Omeka_Record_AbstractRecord) {
        $tags = $recordOrTags->Tags;
    } else {
        $tags = $recordOrTags;
    }

    if (empty($tags)) {
        return '';
    }
    
    $tagStrings = array();
    foreach ($tags as $tag) {
        $name = $tag['name'];
        if (!$link) {
            $tagStrings[] = html_escape($name);
        } else {
            $tagStrings[] = '<a href="' . html_escape(url($link, array('tag' => $name))) . '" rel="tag">' . html_escape($name) . '</a>';
        }
    }
    return join(html_escape($delimiter), $tagStrings);
}

/**
 * Return a URL given the provided arguments.
 * 
 * Instantiates view helpers directly because a view may not be registered.
 *
 * @uses Omeka_View_Helper_Url::url() See for details on usage.
 * @param string|array $options
 * @param string|null|array $name
 * @param array $queryParams
 * @param boolean $reset
 * @param boolean $encode
 * @return string
 */
function url($options = array(), $name = null, $queryParams = array(), 
    $reset = false, $encode = true
) {
    $helper = new Omeka_View_Helper_Url;
    return $helper->url($options, $name, $queryParams, $reset, $encode);
}

/**
 * Return an absolute URL.
 * 
 * This is necessary because Zend_View_Helper_Url returns relative URLs, though 
 * absolute URLs are required in some contexts. Instantiates view helpers 
 * directly because a view may not be registered.
 *
 * @uses url()
 * @param mixed
 * @return string HTML
 */
function absolute_url($options = array(), $route = null, $queryParams = array(), 
    $reset = false, $encode = true
) {
    $serverUrlHelper = new Zend_View_Helper_ServerUrl;
    $urlHelper = new Omeka_View_Helper_Url;
    return $serverUrlHelper->serverUrl() 
         . $urlHelper->url($options, $route, $queryParams, $reset, $encode);
}

/**
 * Return the current URL with query parameters appended.
 *
 * Instantiates view helpers directly because a view may not be registered.
 * 
 * @param array $params
 * @return string
 */
function current_url(array $params = array())
{
    // Get the URL before the ?.
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $urlParts = explode('?', $request->getRequestUri());
    $url = $urlParts[0];
    if ($params) {
        // Merge $_GET and passed parameters to build the complete query.
        $query = array_merge($_GET, $params);
        $queryString = http_build_query($query);
        $url .= "?$queryString";
    }
    return $url;
}

/**
 * Determine whether the given URI matches the current request URI.
 * 
 * Instantiates view helpers directly because a view may not be registered.
 *
 * @param string $url
 * @param Zend_Controller_Request_Http|null $req
 * @return boolean
 */
function is_current_url($url)
{
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $currentUrl = $request->getRequestUri();
    $baseUrl = $request->getBaseUrl();
    
    // Strip out the protocol, host, base URL, and rightmost slash before 
    // comparing the URL to the current one
    $stripOut = array(WEB_DIR, @$_SERVER['HTTP_HOST'], $baseUrl);
    $currentUrl = rtrim(str_replace($stripOut, '', $currentUrl), '/');
    $url = rtrim(str_replace($stripOut, '', $url), '/');
    
    if (strlen($url) == 0) {
        return (strlen($currentUrl) == 0);
    }
    return ($url == $currentUrl) or (strpos($currentUrl, $url) === 0);
}

/**
 * Return a URL to a record.
 *
 * @uses Omeka_View_Helper_RecordUrl::recordUrl()
 * @param Omeka_Record_AbstractRecord|string $record
 * @param string|null $action
 * @param bool $getAbsoluteUrl
 * @return string
 */
function record_url($record, $action = null, $getAbsoluteUrl = false)
{
    return get_view()->recordUrl($record, $action, $getAbsoluteUrl);
}

/**
 * Return a URL to an output page.
 * 
 * @param string $output
 * @param array $otherParams
 * @return string
 */
function items_output_url($output, $otherParams = array()) {
    
    // Copy $_GET and filter out all the cruft.
    $queryParams = $_GET;
    
    // The submit button the search form.
    unset($queryParams['submit_search']);
    
    // If 'page' is passed in query string and not via the route
    // Page should always be the first so that accurate results are retrieved
    // for the RSS.  Does it make sense to get an RSS feed of the 2nd page?
    unset($queryParams['page']);
    
    $queryParams = array_merge($queryParams, $otherParams);
    $queryParams['output'] = $output;
    
    // Use the 'default' route as opposed to the current route.
    return url(array('controller'=>'items', 'action'=>'browse'), 'default', $queryParams);
}

/**
 * Return the provided file's URL.
 * 
 * @param File $file
 * @param string $format
 * @return string
 */
function file_display_url(File $file, $format = 'fullsize')
{
    if (!$file->exists()) {
        return false;
    }
    return $file->getWebPath($format);
}

/**
 * Return a URL to the public theme.
 *
 * @see admin_url()
 * @param mixed
 * @return string
 */
function public_url()
{
    set_theme_base_url('public');
    $args = func_get_args();
    $url = call_user_func_array('url', $args);
    revert_theme_base_url();
    return $url;
}

/**
 * Return a URL to the admin theme.
 * 
 * @see public_url()
 * @param mixed
 * @return string
 */
function admin_url()
{
    set_theme_base_url('admin');
    $args = func_get_args();
    $url = call_user_func_array('url', $args);
    revert_theme_base_url();
    return $url;
}

/**
 * Set the base URL for the specified theme.
 * 
 * @param string $theme
 */
function set_theme_base_url($theme = null)
{
    switch ($theme) {
        case 'public':
            $baseUrl = PUBLIC_BASE_URL;
            break;
        case 'admin':
            $baseUrl = ADMIN_BASE_URL;
            break;
        default:
            $baseUrl = CURRENT_BASE_URL;
            break;
    }
    $front = Zend_Controller_Front::getInstance();
    $front->setParam('previousBaseUrl', $front->getBaseUrl());
    return $front->setBaseUrl($baseUrl);
}

/**
 * Revert the base URL to its previous state.
 */
function revert_theme_base_url()
{
    $front = Zend_Controller_Front::getInstance();
    if (($previous = $front->getParam('previousBaseUrl')) !== null) {
        $front->setBaseUrl($previous);
        $front->clearParams('previousBaseUrl');
    }
}

/**
 * Check the ACL to determine whether the current user has proper permissions.
 *
 * <code>is_allowed('Items', 'showNotPublic')</code>
 * Will check if the user has permission to view Items that are not public.
 *
 * @param string|Zend_Acl_Resource_Interface
 * @param string|null
 * @return boolean
 */
function is_allowed($resource, $privilege)
{
    $acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->acl;
    $user = current_user();

    if (is_string($resource)) {
       $resource = ucwords($resource);
    }

    // User implements Zend_Acl_Role_Interface, so it can be checked directly by the ACL.
    return $acl->isAllowed($user, $resource, $privilege);
}
