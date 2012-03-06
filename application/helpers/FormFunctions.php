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
        $uri = apply_filters('simple_search_default_uri', uri('items/browse'));
    }

    $searchQuery = array_key_exists('search', $_GET) ? $_GET['search'] : '';
    $formProperties['action'] = $uri;
    $formProperties['method'] = 'get';
    $html  = '<form ' . _tag_attributes($formProperties) . '>' . "\n";
    $html .= '<fieldset>' . "\n\n";
    $html .= __v()->formText('search', $searchQuery, array('name'=>'search','class'=>'textinput'));
    $html .= __v()->formSubmit('submit_search', $buttonText);
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
 * @param Omeka_Record $record
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
 * @since 0.10
 * @uses display_form_input_for_element()
 * @param Omeka_Record $record
 * @param string $elementSetName The name of the element set.
 * @return string
 */
function display_element_set_form($record, $elementSetName)
{
    $elements = get_db()->getTable('Element')->findBySet($elementSetName);
    $html = '';
    foreach ($elements as $key => $element) {
       $html .= display_form_input_for_element($element, $record);
    }
    return $html;
}

/**
 * Retrieve validation errors for specific fields on the form.
 *
 * @deprecated deprecated since 1.2
 * @param string $field The name of the field to retrieve the error message for
 * @return string The error message wrapped in a div with class="error"
 */
function form_error($field)
{
    $flash = new Omeka_Controller_Flash;
    if ($flash->getStatus() != Omeka_Controller_Flash::VALIDATION_ERROR) return;
    if (($msg = $flash->getError($field))) {
        return '<div class="error">'.$msg.'</div>';
    }
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

/**
 * Creates a form containing a single button.
 *
 * Do not use this helper if you are already in a form context, use ZF's
 * formSubmit helper instead.
 *
 * @since 1.3
 *
 * @param string $action Form action URI.
 * @param string $name Name/id attribute for button.
 * @param string $value Button value.
 * @param array $attribs Other HTML attributes for button.
 * @param string $formName Name/id attribute for button.
 * @param array $formAttribs Other HTML attributes for button.
 * @return string HTML form.
 */
function button_to($action, $name = null, $value = null, $attribs = array(), $formName = null, $formAttribs = array())
{
    if (!$value) {
        $value = __('Submit');
    }

    $view = __v();
    if (!array_key_exists('action', $formAttribs)) {
        $formAttribs['action'] = $action;
    }
    if (!array_key_exists('method', $formAttribs)) {
        $formAttribs['method'] = 'post';
    }
    if (!array_key_exists('class', $formAttribs)) {
        $formAttribs['class'] = 'button-form';
    }

    // Fieldset tags fix validation errors.
    return $view->form($formName, $formAttribs,
        '<fieldset>' . $view->formSubmit($name, $value, $attribs) . '</fieldset>');
}

/**
 * Creates a form containing a single delete button.
 *
 * Sets a class and default URI for a delete button.
 *
 * Do not use this helper if you are already in a form context, use ZF's
 * formSubmit helper instead.
 *
 * @since 1.3
 *
 * @param string|Omeka_Record $action Form action URI. If an Omeka_Record is
 *  passed, uses record_uri to form a link to the delete action for that record.
 * @param string $name Name/id attribute for button.
 * @param string $value Button value.
 * @param array $attribs Other HTML attributes for button.
 * @param string $formName Name/id attribute for button.
 * @param array $formAttribs Other HTML attributes for button.
 * @return string HTML form.
 */
function delete_button($action = null, $name = null, $value = null, $attribs = array(), $formName = null, $formAttribs = array())
{
    if (!$value) {
        $value = __('Delete');
    }

    if (!isset($action)) {
        // If nothing is set at all, use the current route's delete action.
        $action = uri(array('action' => 'delete-confirm'));
    } else if ($action instanceof Omeka_Record) {
        // If a record is given, use record_uri to get the action.
        $action = record_uri($action, 'delete-confirm');
    }

    if (!array_key_exists('class', $attribs)) {
        $attribs['class'] = 'delete-confirm';
    }

    return button_to($action, $name, $value, $attribs, $formName, $formAttribs);
}
