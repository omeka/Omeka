<?php 
//Useful global library functions
function get_option($name) {
		$options = Zend_Registry::get('options');
		return $options[$name];
}

function set_option($name, $value)
{
	$db = get_db();
	$db->exec("REPLACE INTO $db->Option (name, value) VALUES (?,?)", array($name, $value));
	
	//Now update the options hash so that any subsequent requests have it available
	$options = Zend_Registry::get('options');
	$options[$name] = $value;
	
	Zend_Registry::set('options', $options);
}

function generate_slug($text)
{
	$slug = trim($text);
	
	//Replace prohibited characters in the title with - 's
	$prohibited = array(':', '/', ' ', '.');
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
	return Omeka::loggedIn();
}

function get_db()
{
	return Zend_Registry::get('db');
}

/**
 * @copyright Wordpress 2007 (GPL)
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
?>