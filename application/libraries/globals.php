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
    $args = func_get_args();
    $hook = array_shift($args);
    return call_user_func_array(array(get_plugin_broker(), $hook), $args);
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

/**
 * @deprecated Please put your view scripts in the views/$theme directory in
 * your plugin, where $theme could be 'admin', 'public' or 'shared'.
 * @param string
 * @param string
 * @return void
 **/
function add_theme_pages($dir, $theme='both')
{
    get_plugin_broker()->addThemeDir($dir, $theme);
}

/**
 * @deprecated
 * @param string
 * @return void
 **/
function add_controllers($dir='controllers')
{
    get_plugin_broker()->addControllerDir($dir);
}

/**
 * @deprecated
 * @param string
 * @param array 
 * @return void
 **/
function add_data_feed($format, $options=array())
{
    get_plugin_broker()->addFeed($format, $options);
}

/**
 * @deprecated Please use filters for adding navigation instead.
 * @return void
 **/
function add_navigation($text, $link, $type='main', $permissions=null)
{
    trigger_error('add_navigation() has been deprecated.  To add navigation via plugins, please use filters instead.');
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
    $extraOptions = array_slice(func_get_args(), 2);
    return get_plugin_broker()->applyFilters($filterName, $valueToFilter, $extraOptions);
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
    get_plugin_broker()->addFilter($filterName, $callback, $priority);
}

/**
 * @return Omeka_Acl
 **/
function get_acl()
{
    return Omeka_Context::getInstance()->getAcl();
}

function add_plugin_directories()
{
    get_plugin_broker()->addApplicationDirs();
}