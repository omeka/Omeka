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
add_filter(array('Display', 'Collection', 'Dublin Core', 'Title'), 'show_untitled_items');

add_plugin_hook('public_head', 'theme_header_background');

// If there is a current user, add admin_bar.
if (!is_admin_theme() && apply_filters('public_show_admin_bar', (bool) current_user() )) {
    add_plugin_hook('public_head', 'admin_bar_css');
    add_plugin_hook('public_body', 'admin_bar');
    add_filter('body_tag_attributes', 'admin_bar_class');
}
