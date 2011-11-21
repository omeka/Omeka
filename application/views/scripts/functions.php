<?php

function utf8_htmlspecialchars($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function show_untitled_items($title)
{
    // Remove all whitespace and formatting before checking to see if the title 
    // is empty.
    $prepTitle = trim(strip_formatting($title));
    if (empty($prepTitle)) {
        return __('[Untitled]');
    }
    return $title;
}

/**
* This function checks the Logo theme option, then returns either an
* image tag with the logo as the src, or returns null.
*
*/
function custom_display_logo()
{
    if(function_exists('get_theme_option')) {
    
        $logo = get_theme_option('Logo');

        if ($logo) {
            $storage = Zend_Registry::get('storage');
            $uri = $storage->getUri($storage->getPathByType($logo, 'theme_uploads'));
            return '<img src="'.$uri.'" title="'.settings('site_title').'" />';
        }
    }
    return null;
}

function custom_show_item_metadata(array $options = array(), $item = null)
{
    if (!$item) {
        $item = get_current_item();
    }
    if ($dcFieldsList = get_theme_option('display_dublin_core_fields')) {

        $otherElementSets = array();

        $elementSets = get_db()->getTable('ElementSet')->findForItems();
        foreach ($elementSets as $set) {
            if ($set->name == 'Dublin Core') continue;
            $otherElementSets[] = $set->name;
        }

        $html = '<h2>' . __('Dublin Core') . '</h2>';
        $dcFields = explode(',', $dcFieldsList);
        foreach ($dcFields as $field) {
            $field = trim($field);
            if (element_exists('Dublin Core', $field)) {
                if ($fieldValues = item('Dublin Core', $field, 'all')) {
                    $html .= '<h3>'.__($field).'</h3>';
                    foreach ($fieldValues as $key => $fieldValue) {
                        if (!item_field_uses_html('Dublin Core', $field, $key)) {
                            $fieldValue = nls2p($fieldValue);
                        }
                        $html .= $fieldValue;
                    }
                }
            }
        }
        $html .= show_item_metadata(array('show_element_sets' => $otherElementSets));
        return $html;
    } else {
        return show_item_metadata($options, $item); 
    }
}

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
        $navArray = array(__('Browse Items') => uri('items'), __('Browse Collections') =>uri('collections'));
        return public_nav_main($navArray);
    }
}

function custom_header_image()
{
    if(function_exists('get_theme_option') && $headerBg = get_theme_option('Header Image')) {
        $storage = Zend_Registry::get('storage');
        $headerBg = $storage->getUri($storage->getPathByType($headerBg, 'theme_uploads'));
        $html = '<div id="header-image"><img src="'.$headerBg.'" /></div>';
        return $html;	
    }
    return false;
}

function custom_header_background()
{
    if(function_exists('get_theme_option') && $headerBg = get_theme_option('Header Background')) {
        $storage = Zend_Registry::get('storage');
        $headerBg = $storage->getUri($storage->getPathByType($headerBg, 'theme_uploads'));
        $html = "<style type=\"text/css\" media=\"screen\">"
              . " #header {"
              . "    background:transparent url('$headerBg') top left no-repeat;"
              . "}"
              . "</style>";
        echo $html;
    }
}

function custom_nav_items($navArray = array())
{
    if (!$navArray) {
        $navArray = array(__('Browse All') => uri('items'), __('Browse by Tag') => uri('items/tags'));
    }

    // Check to see if the function public_nav_items, introduced in Omeka 1.3, exists.
    if (function_exists('public_nav_items')) {
        return public_nav_items($navArray);
    } else {
        return nav($navArray);
    }
}
