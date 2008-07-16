<?php 
add_filter('html_escape', 'htmlentities');

add_filter(array('Display', 'Item', 'id'), 'display_item_id_numerically');

function display_item_id_numerically($id)
{
    return '#' . $id;
}

add_filter(array('Display', 'Item', 'Title'), 'show_untitled_items');

function show_untitled_items($title)
{
    if (empty($title)) {
        return '[Untitled]';
    }
    return $title;
}