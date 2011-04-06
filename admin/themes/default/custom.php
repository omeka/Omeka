<?php 
// This messes up the form inputs that use the item's ID, but it still serves as
// an example of what the system can do.

// add_filter(array('Display', 'Item', 'id'), 'display_item_id_numerically');
// 
// function display_item_id_numerically($id)
// {
//     return '#' . $id;
// }

add_filter(array('Display', 'Item', 'Dublin Core', 'Title'), 'show_untitled_items');

require_once dirname(__FILE__) . '/functions.php';
