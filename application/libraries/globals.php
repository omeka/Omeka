<?php
/**
 * Helper functions that are always available in Omeka.  As global functions,
 * these should be used as little as possible in the application code
 * to reduce coupling.
 *
 * @package Omeka
 **/
 
function get_option($name) {
		$options = Omeka_Context::getInstance()->getOptions();
		return $options[$name];
}

function set_option($name, $value)
{
	$db = get_db();
	$db->exec("REPLACE INTO $db->Option (name, value) VALUES (?,?)", array($name, $value));
	
	//Now update the options hash so that any subsequent requests have it available
	$options = Omeka_Context::getInstance()->getOptions();
	$options[$name] = $value;
	
	Omeka_Context::getInstance()->setOptions($options);
}

function generate_slug($text)
{
	$slug = trim($text);
	
	//Replace prohibited characters in the title with - 's
	$prohibited = array(':', '/', ' ', '.', '#');
	$replace = array_fill(0, count($prohibited), '-');
	$slug = str_replace($prohibited, $replace, strtolower($slug) );
	return $slug;
}

function pluck($col, $array)
{
	$res = array();
	foreach ($array as $k => $row) {
		$res[$k] = $row[$col];
	}
	return $res;	
} 

function current_user()
{
	return Omeka_Context::getInstance()->getCurrentUser();
}

function get_db()
{
	return Omeka_Context::getInstance()->getDb();
}

/**
 * Useful for debugging things.
 * 
 * @note This will die fiery death if the logger is not enabled
 * @param string
 * @return void
 **/
function debug($msg)
{
    $context = Omeka_Context::getInstance();
    $logger = $context->getLogger();
    if($logger) {
        $logger->debug($msg);
    }
}

/**
 *
 * @return mixed
 **/
function stripslashes_deep($value)
{
	 $value = is_array($value) ?
							 array_map('stripslashes_deep', $value) :
							 stripslashes($value);

	 return $value;
}

function add_plugin_hook($hook, $callback)
{
    get_plugin_broker()->addHook($hook, $callback);
} 

/**
 * fire_plugin_hook('save_item', $item, $arg2)  would call the plugin hook 'save_item' with those 2 arguments
 *
 * @return void
 **/
function fire_plugin_hook()
{
    $args = func_get_args();
    
    $hook = array_shift($args);
        
    return call_user_func_array(array(get_plugin_broker(), $hook), $args);
}

/**
 * @return Omeka_Plugin_Broker|null
 **/
function get_plugin_broker()
{
    return Omeka_Context::getInstance()->getPluginBroker();
}

/**
 * Define a metafield through the plugin interface.
 * 
 * @param string
 * @param string
 * @param string
 * @return void
 **/
function define_metafield($name, $description, $type=null)
{
    get_plugin_broker()->defineMetafield($name, $description, $type);
}

function add_theme_pages($dir, $theme='both')
{
    get_plugin_broker()->addThemeDir($dir, $theme);
}

function add_controllers($dir='controllers')
{
    get_plugin_broker()->addControllerDir($dir);
}

function add_data_feed($format, $options=array())
{
    get_plugin_broker()->addFeed($format, $options);
}

function add_navigation($text, $link, $type='main', $permissions=null)
{
    get_plugin_broker()->addNavigation($text, $link, $type, $permissions);
}

function add_mime_display_type($mimeTypes, $callback, array $options=array())
{
    get_plugin_broker()->addMediaAdapter($mimeTypes, $callback, $options);
}

function get_acl()
{
    return Omeka_Context::getInstance()->getAcl();
}

function add_plugin_directories()
{
    get_plugin_broker()->addApplicationDirs();
}