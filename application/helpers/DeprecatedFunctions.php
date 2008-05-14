<?php
/**
 *
 * @deprecated 
 * @param string $urlEnd The controller/action/parameter that specifies the link.
 * @example uri('items/browse/'.$item->id); 
 * @todo Work without mod_rewrite enabled: uri('items/show/3') -> ?u=items/show/3
 * @return string Url for the link href attribute.
 **/
function uri($urlEnd, $params=array())
{    
    return url_for($urlEnd, null, $params);
}

/**
 * @deprecated
 *
 * @return string
 **/
function generate_url($options, $name)
{
    trigger_error('generate_url() is deprecated, please use url_for() instead!');
	return url_for($options, $name);
}

/**
 * Output the dublin core field description
 * 
 * @deprecated
 * @param string
 * @return void
 **/
function dublin_core($type) { 
	$data = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'dublincore.ini', array('coremetadata')); 
	return h($data->$type); 
} 

/**
 * Return either the value or, if it's empty, output the default
 * 
 * @deprecated This shouldn't be here in the themes API (it's more like a global helper function)
 * @param mixed
 * @return mixed
 **/
function not_empty_or($value, $default) {
	return !empty($value) ? $value : $default;
}

/**
 * This works different from the above function in that it may/may not append navigation
 * via the plugins, but also different in the way it handles CSS.  Instead of class="current"
 * because of all the whacked-out navigation on the admin theme, we give each link an class of 'nav-'
 * + the link text.
 *
 **/
function admin_nav(array $links) {
	$current = $_SERVER['REQUEST_URI'];
	
	$nav = '';
	foreach ($links as $text => $link) {
		$nav .= '<li class="' . text_to_id($text, 'nav') . '"><a href="' . $link . '">' . h($text) . '</a></li>' . "\n";
	}
	return $nav;	
}

/**
 * 
 *
 * @return string
 **/
function items_rss_header()
{
	if($_GET and is_current(uri('items/browse'))) {
		$uri = items_rss_uri($_GET);
	}else {
		$uri = items_rss_uri();
	}
	
	return '<link rel="alternate" type="application/rss+xml" title="'.get_option('site_title').'" href="'. $uri .'" />';
}

//Format of $date is YYYY-MM-DD
function get_month($date)
{
	$parts = explode('-',$date);
	if($parts[1] === '00') return null;
	return $parts[1];
}

function get_day($date)
{
	$parts = explode('-',$date);
	if($parts[2] === '00') return null;
	return $parts[2];
}

function get_year($date)
{
	$parts = explode('-',$date);
	if($parts[0] === '0000') return null;
	return $parts[0];
}

function item_metadata($item, $field, $escape=true)
{
	$text = $item->getMetatext($field);
	
	return $escape ? h($text) : $text;
}

/**
 * Display an alternative value if the given variable is empty
 *
 * @return string
 **/
function display_empty($val, $alternative="[Empty]") {
	return nls2p(h(!empty($val) ? $val : $alternative));
}
