<?php 
add_filter('html_escape', 'htmlentities');

// This messes up the form inputs that use the item's ID, but it still serves as
// an example of what the system can do.

// add_filter(array('Display', 'Item', 'id'), 'display_item_id_numerically');
// 
// function display_item_id_numerically($id)
// {
//     return '#' . $id;
// }

add_filter(array('Display', 'Item', 'Title'), 'show_untitled_items');

function show_untitled_items($title)
{
    if (empty($title)) {
        return '[Untitled]';
    }
    return $title;
}

// A filter for displaying the <select> menu for Language on the Item form.
add_filter(array('Form', 'Item', 'Language', 'Dublin Core'), 'display_language_form_input');

function display_language_form_input($html, $inputNameStem, $language, $options, $item, $element)
{
    $languageChoices = array(
		'eng'=>'English', 
		'rus'=>'Russian',
		'deu'=>'German',
		'fra'=>'French',
		'spa'=>'Spanish',
		'san'=>'Sanskrit');
    return __v()->formSelect($inputNameStem . '[text]', $language, null, $languageChoices);
}

