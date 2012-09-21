<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage FormHelpers
 */

/**
 * Generate attributes for XHTML tags.
 *
 * @since 0.9
 * @access private
 * @param array|string $attributes Attributes for the tag.  If this is a
 * string, it will assign both 'name' and 'id' attributes that value for
 * the tag.
 * @param string $value
 * @return string
 */
function _tag_attributes($attributes, $value=null)
{
    if (is_string($attributes)) {
        $toProcess['name'] = $attributes;
        $toProcess['id'] = $attributes;
    } else {
        //don't allow 'value' to be set specifically as an attribute (why = consistency)
        unset($attributes['value']);
        $toProcess = $attributes;
    }

    $attr = array();
    foreach ($toProcess as $key => $attribute) {
        // Only include the attribute if its value is a string.
        if (is_string($attribute)) {
            $attr[$key] = $key . '="' . html_escape( $attribute ) . '"';
        }
    }
    return join(' ',$attr);
}

/**
 * Make a simple search form for the items.
 *
 * Contains a single fieldset with a text input and submit button.
 *
 * @since 0.9
 * @param string $buttonText Optional Defaults to 'Search'.
 * @param array $formProperties Optional XHTML attributes for the form.  Defaults
 * to setting id="simple-search".
 * @param string $uri Optional Action for the form.  Defaults to 'items/browse'.
 * @return string
 */
function simple_search($buttonText = null, $formProperties=array('id'=>'simple-search'), $uri = null)
{
    if (!$buttonText) {
        $buttonText = __('Search');
    }

    // Always post the 'items/browse' page by default (though can be overridden).
    if (!$uri) {
        $uri = apply_filters('simple_search_default_uri', url('items/browse'));
    }

    $searchQuery = array_key_exists('search', $_GET) ? $_GET['search'] : '';
    $formProperties['action'] = $uri;
    $formProperties['method'] = 'get';
    $html  = '<form ' . _tag_attributes($formProperties) . '>' . "\n";
    $html .= '<fieldset>' . "\n\n";
    $html .= __v()->formText('search', $searchQuery);
    $html .= __v()->formSubmit('submit_search', $buttonText, array('class' => 'blue'));
    $html .= '</fieldset>' . "\n\n";

    // add hidden fields for the get parameters passed in uri
    $parsedUri = parse_url($uri);
    if (array_key_exists('query', $parsedUri)) {
        parse_str($parsedUri['query'], $getParams);
        foreach($getParams as $getParamName => $getParamValue) {
            $html .= __v()->formHidden($getParamName, $getParamValue);
        }
    }

    $html .= '</form>';
    return $html;
}

/**
 * Retrieve the proper HTML for a form input for a given Element record.
 *
 * Assume that the given element has access to all of its values (for example,
 * all values of a Title element for a given Item).
 *
 * This will output as many form inputs as there are values for a given
 * element.  In addition to that, it will give each set of inputs a label and
 * a span with class="tooltip" containing the description for the element.
 * This span can either be displayed, hidden with CSS or converted into a
 * tooltip with javascript.
 *
 * All sets of form inputs for elements will be wrapped in a div with
 * class="field".
 *
 * @since 0.10
 * @param Element|array $element
 * @param Omeka_Record_AbstractRecord $record
 * @param array $options Optional
 * @return string HTML
 */
function display_form_input_for_element($element, $record, $options = array())
{
    $html = '';
    // If we have an array of Elements, loop through the form to display them.
    if (is_array($element)) {
        foreach ($element as $key => $e) {
            $html .= __v()->elementForm($e, $record, $options);
        }
    } else {
        $html = __v()->elementForm($element, $record, $options);
    }
    return $html;
}

/**
 * Used within the admin theme (and potentially within plugins) to display a form
 * for a record for a given element set.
 *
 * @uses display_form_input_for_element()
 * @param String $recordType
 * @param Omeka_Record_AbstractRecord $record
 * @param string $elementSetName The name of the element set.
 * @return string
 */
function display_element_set_form($recordType, $record, $elementSetName)
{
    $elements = get_db()->getTable('Element')->findBySet($elementSetName);
        
    $filterName = array('Form', $recordType, $elementSetName);
    $elements = apply_filters(
        $filterName, 
        $elements,
        array('recordType' => $recordType, 'record' => $record, 'elementSetName' => $elementSetName)
    );
            
    $html = '';
    foreach ($elements as $key => $element) {
       $html .= display_form_input_for_element($element, $record);
    }
    return $html;
}


/**
 * Used within the admin theme (and potentially within plugins) to display a form
 * for an item's item type
 *
 * @uses display_form_input_for_element()
 * @param Item $item
 * @return string
 */
function display_item_type_elements_for_item_form($item)
{    
    $itemType = $item->getItemType();
    $filterName = array('Form', 'ItemTypeForItem', $itemType->name);
    $elements = $item->getItemTypeElements();
    $elements = apply_filters(
        $filterName,
        $elements,
        array('item' => $item)
    );
    
    //Loop through all of the element records for the item's item type
    $html = '';
    foreach ($elements as $key => $element) {
       $html .= display_form_input_for_element($element, $item);
    }    
    return $html;
}


/**
 * Adds the "Select Below" or other label option to a set of select
 * options.
 *
 * @param array $options
 * @param string|null $labelOption
 * @return array
 */
function label_options($options, $labelOption = null)
{
    if ($labelOption === null) {
        $labelOption = __('Select Below ');
    }
    return array('' => $labelOption) + $options;
}

/**
 * Get the options array for a given table.
 *
 * @param string $tableClass
 * @param string $labelOption
 * @param array $searchParams Optional search parameters on table.
 */
function get_table_options($tableClass, $labelOption = null, $searchParams = array())
{
    $options = get_db()->getTable($tableClass)->findPairsForSelectForm($searchParams);
    $options = apply_filters(Inflector::underscore($tableClass) . '_select_options', $options);
    return label_options($options, $labelOption);
}
