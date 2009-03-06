<?php
/**
 * Helper functions that are always available in Omeka.  As global functions,
 * these should be used as little as possible in the application code
 * to reduce coupling.
 *
 * @package Omeka
 **/
 
/**
 * @return string
 **/ 
function get_option($name) {
    $options = Omeka_Context::getInstance()->getOptions();
    return $options[$name];
}

/**
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
 * @access private
 * @param string
 * @return void
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
 * @return User|null
 **/
function current_user()
{
    return Omeka_Context::getInstance()->getCurrentUser();
}

/**
 * @return Omeka_Db
 **/
function get_db()
{
    return Omeka_Context::getInstance()->getDb();
}

/**
 * Useful for debugging things.
 * 
 * @access private
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
 * @access private
 * @return mixed
 **/
function stripslashes_deep($value)
{
     $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);

     return $value;
}

/**
 * @param string
 * @param callback
 * @return void
 **/
function add_plugin_hook($hook, $callback)
{
    get_plugin_broker()->addHook($hook, $callback);
} 

/**
 * fire_plugin_hook('after_save_item', $item, $arg2)  would call the plugin hook 
 * 'after_save_item' with those 2 arguments.
 *
 * @access private
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
 * 
 * @param string
 * @return void
 **/
function add_mime_display_type($mimeTypes, $callback, array $options=array())
{
    get_plugin_broker()->addMediaAdapter($mimeTypes, $callback, $options);
}

/**
 * @since 6/13/08
 * @uses Omeka_Plugin_Filters::applyFilters()
 * @param string|array
 * @param mixed
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
 * @since 6/13/08
 * @param string|array
 * @param callback
 * @param integer
 * @return void
 **/
function add_filter($filterName, $callback, $priority = 10)
{
    if ($pluginBroker = get_plugin_broker()) {
        $pluginBroker->addFilter($filterName, $callback, $priority);
    }
}

/**
 * @return Omeka_Acl
 **/
function get_acl()
{
    return Omeka_Context::getInstance()->getAcl();
}

function is_admin_theme()
{
    return defined('ADMIN');
}

/**
 * A prototype of the insert_item() helper, which will be in the core in 1.0.
 *
 * @uses InsertItemHelper
 * @param array $itemMetadata 
 * @param array $elementTexts 
 * @return Item
 * @throws Omeka_Validator_Exception
 * @throws Exception
 * 
 * $metadata = array(
 *     'public'         => [true|false], 
 *     'featured'       => [true|false], 
 *     'collection_id'  => [int],
 *     'item_type_id'   => [int],
 *     'item_type_name' => [string]
 * );
 * $elementTexts = array(
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
 * @see InsertItemHelper::addFiles()
 * @param 
 * @return mixed
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
 * @param array $itemMetadata Set of options that can be passed to the item.
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
 * $metadata = array(
 *     'name'       => [string], 
 *     'description'=> [string], 
 *     'public'     => [true|false], 
 *     'featured'   => [true|false]
 * );
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
 * Helper funtion for inserting an element set and its elements into the 
 * database.
 * 
 * @param string|array $elementSet Element set information.
 *     [(string) element set name]
 *     -OR-
 *     array(
 *         'name'        => [(string) element set name, required, unique], 
 *         'description' => [(string) element set description, optional]
 *     );
 * @param array $elements An array containing element data. There are three 
 * ways to include elements. 1) An array containing element data; 2) A string 
 * of the element name; 3) A new or existing Element record object.
 *     array(
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
 * Use this fuction after you are done using an Omeka model object to prevent memory leaks
 * Required because PHP 5.2 does not do garbage collection on circular references.
 *
 * @param mixed 
 *
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