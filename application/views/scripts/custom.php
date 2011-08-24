<?php 
require_once dirname(__FILE__) . '/functions.php';

// When displaying item metadata (or anything else that makes use of the 
// 'html_escape' filter), make sure we escape entities correctly with UTF-8.
add_filter('html_escape', 'utf8_htmlspecialchars', 1);

// The second thing that needs to happen for all metadata that needs to be escaped
// to HTML is that new lines need to be represented as <br /> tags.
add_filter('html_escape', 'nl2br', 2);

/**
 * If an item has a blank Dublin Core Title, use the string '[Untitled]' instead.
 */
add_filter(array('Display', 'Item', 'Dublin Core', 'Title'), 'show_untitled_items');

add_plugin_hook('public_theme_header', 'custom_header_background');
