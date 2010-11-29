<?php

if (!function_exists('utf8_htmlentities')) {
    function utf8_htmlentities($value)
    {
        return htmlentities($value, ENT_QUOTES, "UTF-8");
    }
}

add_filter(array('Display', 'Item', 'Dublin Core', 'Title'), 'show_untitled_items');

if (!function_exists('show_untitled_items')) {
    function show_untitled_items($title)
    {
        // Remove all whitespace and formatting before checking to see if the title 
        // is empty.
        $prepTitle = trim(strip_formatting($title));
        if (empty($prepTitle)) {
            return '[Untitled]';
        }
        return $title;
    }
}

/**
 * This function checks the Logo theme option, then returns either an
 * image tag with the logo as the src, or returns null.
 *
 **/
if (!function_exists('custom_display_logo')) {
 
    function custom_display_logo()
    {
        if(function_exists('get_theme_option')) {
        
            $logo = get_theme_option('Logo');
        
            $logoPath = WEB_THEME_UPLOADS.DIRECTORY_SEPARATOR.$logo;
        
    	    $siteTitle = $logo ? '<img src="'.$logoPath.'" title="'.settings('site_title').'" />' : null;
	
    	    return $siteTitle;
        }
    
        return null;
    }
}

if (!function_exists('custom_show_item_metadata')) {

    function custom_show_item_metadata(array $options = array(), $item = null)
    {
        if (!$item) {
            $item = get_current_item();
        }
    	if ($dcFieldsList = get_theme_option('display_dublin_core_fields')) {
    	    $html = '';
    	    $dcFields = explode(',', $dcFieldsList);
    	    foreach ($dcFields as $field) {
    	        $field = trim($field);
    	        if (element_exists('Dublin Core', $field)) {
        	        if ($fieldValue = item('Dublin Core', $field)) {
        	            $html .= '<h3>'.$field.'</h3>';
        	            if (!item_field_uses_html('Dublin Core', $field)) {
        	                $fieldValue = nls2p($fieldValue);
        	            }
        	            $html .= $fieldValue;
    	            
        	        }
        	    }
    	    }
    	    $html .= show_item_metadata(array('show_element_sets' => array('Item Type Metadata')));
    	    return $html;
    	} else {
    	    return show_item_metadata($options, $item); 
        }
    }
    
}

if (!function_exists('custom_public_nav_header')) {

    function custom_public_nav_header()
    {    
        if ($customHeaderNavigation = get_theme_option('custom_header_navigation')) {
            $navArray = array();
            $customLinkPairs = explode("\n", $customHeaderNavigation);
            foreach ($customLinkPairs as $pair) {
                $pair = trim($pair);
                if ($pair != '') {
                    $pairArray = explode('|', $pair, 2);
                    if (count($pairArray) == 2) {
                        $link = trim($pairArray[0]);
                        $url = trim($pairArray[1]); 
                        if (strncmp($url, 'http://', 7) && strncmp($url, 'https://', 8)){                        
                            $url = uri($url);
                        }
                    }
                    $navArray[$link] = $url;
                }
            }
            return nav($navArray);
        } else {
            $navArray = array('Browse Items' => uri('items'), 'Browse Collections'=>uri('collections'));
            return public_nav_main($navArray);
        }
    }
}

if (!function_exists('custom_header_image')) {
    function custom_header_image()
    {
        if(function_exists('get_theme_option') && $headerBg = get_theme_option('Header Image')) {
            $headerBg = WEB_THEME_UPLOADS.DIRECTORY_SEPARATOR.$headerBg;
            $html = '<div id="header-image"><img src="'.$headerBg.'" /></div>';
            return $html;	
        }
        return false;
    }
}

if (!function_exists('custom_nav_items')) {

    function custom_nav_items($navArray = array())
    {
        if (!$navArray) {
            $navArray = array('Browse All' => uri('items'), 'Browse by Tag' => uri('items/tags'));
        }
    
        // Check to see if the function public_nav_items, introduced in Omeka 1.3, exists.
        if (function_exists('public_nav_items')) {
    		return public_nav_items($navArray);
    	} else {
    	    return nav($navArray);
    	}
    }
}