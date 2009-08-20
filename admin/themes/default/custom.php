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

function implode_array_to_english($array) {
    // sanity check
    if (!$array || !count ($array))
        return '';

    // get last element   
    $last = array_pop ($array);

    // if it was the only element - return it
    if (!count ($array))
        return $last; 
    
    // If there were only two elements in the array
    if(count($array) == 1) 
        return implode('', $array).' and '.$last;  

    return implode (', ', $array).' and '.$last;
}