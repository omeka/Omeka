<?php

// Use ENT_SUBSTITUTE when we're using a new-enough PHP version
if (defined('ENT_SUBSTITUTE')) {
    function utf8_htmlspecialchars($value)
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
} else {
    function utf8_htmlspecialchars($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
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
 * Partial for the admin bar.
 */
function admin_bar() {
    echo common('admin-bar');
}

/**
 * Styles for admin bar.
 */
function admin_bar_css() {
    queue_css_url('//fonts.googleapis.com/css?family=Arvo:400', 'screen');
    queue_css_file('admin-bar', 'screen');
}

/**
 * Adds 'admin-bar' to the class attribute for the body tag.
 */
function admin_bar_class($attributes) {
    $attributes['class'] = trim('admin-bar '.$attributes['class']);
    return $attributes;
}
