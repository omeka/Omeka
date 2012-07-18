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
