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
 * Make a label for a form element.
 *
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param mixed $attributes An array of attributes, or just the string id to be used in the 'for' attribute
 * @param string $text Text of the form label
 * @return string
 */
function label($attributes, $text)
{
    if (is_string($attributes)) {
        $name = $attributes;
        $attributes = array();
    } else if (is_array($attributes)) {
       $name = $attributes['name'];
    } else if (!$attributes) {
        $attributes = array();
    }
    return __v()->formLabel($name, $text, $attributes);
}

/**
 * Make a text form input.
 *
 * @internal Facade for Zend_View_Helper_FormText.  This maintains the
 * signature from prior versions for backward compatibility.
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param string|null $default
 * @param string|null $label
 * @param array $labelAttributes Since 1.2. Optional XHTML attributes for the label.
 * @return string HTML for the form element
 */
function text($attributes, $default=null, $label = null, $labelAttributes = array())
{
    $html = '';
    if($label) {
        // This is a hack to only apply the 'class' attribute to the input
        // and not to the label
        $labelAttributes['for'] = $attributes['name'];
        $html .= __v()->formLabel($attributes['name'], $label, $labelAttributes);
    }

    $html .= __v()->formText($attributes['name'], $default, $attributes);
    return $html;
}

/**
 * Make a password form input.
 *
 * @internal Facade for Zend_View_Helper_FormPassword.
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes XHTML attributes for the password input.
 * @param string|null $default Optional
 * @param string|null $label Optional
 * @param array $labelAttributes Since 1.2. Optional XHTML attributes for the label
 * @return string
 */
function password($attributes, $default=null, $label = null, $labelAttributes = array())
{
    $html = '';
    if ($label) {
        $labelAttributes['for'] = $attributes['name'];
        $html .= __v()->formLabel($attributes['name'], $label, $attributes);
    }
    $html .= __v()->formPassword($attributes['name'], $default, $attributes);
    return $html;
}

/**
 * Make a select form input.
 *
 * This will add 'Select Below' as the first (empty) element in the list of
 * values.
 *
 * @internal Facade for Zend_View_Helper_FormSelect.
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param array|null $values Optional
 * @param string|null $default Optional
 * @param string|null $label Optional
 * @param array $labelAttributes Since 1.2. Optional XHTML attributes for the label
 * @return string
 */
function select($attributes, $values = null, $default = null, $label=null, $labelAttributes = array())
{
    $html = '';
    //First option is always the "Select Below" empty entry
    $values = (array) $values;
    $values = array('' => __('Select Below ')) + $values;
    //Duplication
    if ($label) {
        $labelAttributes['for'] = $attributes['name'];
        $html .= __v()->formLabel($attributes['name'], $label, $labelAttributes);
    }
    $html .= __v()->formSelect($attributes['name'], $default, $attributes, $values);
    return $html;
}

/**
 * Make a textarea form input.
 *
 * @internal Facade for Zend_View_Helper_FormTextarea.
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param string|null $default Optional
 * @param string|null $label Optional
 * @param array $labelAttributes Since 1.2. Optional XHTML attributes for the label.
 * @return string
 */
function textarea($attributes, $default = null, $label = null, $labelAttributes = array())
{
    $html = '';
    if ($label) {
        $labelAttributes['for'] = $attributes['name'];
        $html .= __v()->formLabel($attributes['name'], $label, $labelAttributes);
    }
    $html .= __v()->formTextarea($attributes['name'], $default, $attributes);
    return $html;
}

/**
 * Make a form input that is a set of radio buttons.
 *
 * @internal Facade for Zend_View_Helper_FormRadio.
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes Set of XHTML attributes for the inputs.
 * @param array $values Key => value of the radio button, Value => label for the
 * radio button.
 * @param string|null $default Optional
 * @param string $label_class Optional Defaults to 'radiolabel'.
 * @return string HTML
 */
function radio($attributes, array $values, $default = null, $label_class = 'radiolabel')
{
    $attributes['label_class'] = $label_class;
    return __v()->formRadio($attributes['name'], $default, $attributes, $values, null);
}

/**
 * Make a hidden form input.
 *
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param string $value
 * @return string
 */
function hidden($attributes, $value)
{
    $input = '<input type="hidden"';
    if (!empty($attributes)) {
        $input .= ' '._tag_attributes($attributes);
    }
    $input .= ' value="'.html_escape($value).'"';
    $input .= ' />' . "\n";
    return $input;
}

/**
 * Make a checkbox form input.
 *
 * @internal Facade for Zend_View_Helper_FormCheckbox.
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes XHTML attributes for the input.
 * @param boolean|null $checked Whether or not it should be checked by default.
 * @param string|null $value Optional Defaults to 1.
 * @param string|null $label Optional
 * @param array $labelAttributes Since 1.2. Optional XHTML attributes for the label.
 * @return string
 */
function checkbox($attributes, $checked = FALSE, $value=null, $label = null, $labelAttributes = array())
{
    if ($checked !== null) {
        $attributes['checked'] = $checked;
    }
    $html = __v()->formCheckbox($attributes['name'], $value, $attributes);
    if ($label) {
        $labelAttributes['for'] = $attributes['name'];
        $html .= __v()->formLabel($attributes['name'], $label, $labelAttributes);
    }
    return $html;
}

/**
 * Make a submit form input.
 *
 * @since 0.9
 * @deprecated deprecated since 1.2
 * @param array $attributes XHTML attributes.
 * @param string $value Optional Defaults to 'Submit'.
 * @return string
 */
function submit($attributes, $value = null)
{
    if (!$value) {
        $value = __('Submit');
    }

    // This is a hack that makes this work.
    $otherAttribs = array();
    if (is_array($attributes)) {
        $otherAttribs = $attributes;
    }
    return __v()->formSubmit($attributes, $value, $otherAttribs);
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
 * @since 0.10
 * @param array $props Optional
 * @param mixed $value Optional
 * @param string|null $label Optional
 * @param array $search Optional
 * @uses _select_from_table()
 * @return string
 */
function select_user($props = array(), $value=null, $label=null, $search = array())
{
    return _select_from_table('User', $props, $value, $label, $search);
}

/**
 * Select the Item Type for the current Item.
 *
 * This probably won't be used by theme writers because it only applies to the
 * items form.
 *
 * @since 0.10
 * @param array
 * @param Item|null Check for this specific item record (current item if null).
 * @return string HTML for the form input.
 */
function select_item_type_for_item($props=array(), $item=null)
{
    if (!$item) {
        $item = get_current_item();
    }
    return select_item_type($props, $item->item_type_id);
}

/**
 * @since 0.10
 * @param array $props Optional
 * @param string|null $value Optional
 * @param string|null $label Optional
 * @param array $search Optional
 * @return string
 */
function select_collection($props = array(), $value=null, $label=null, $search = array())
{
    return _select_from_table('Collection', $props, $value, $label, $search);
}

/**
 * @since 0.10
 * @param array $props Optional XHTML attributes for the select.
 * @param mixed $value Optional Default value of the select.
 * @param string|null $label Optional Label for the select.
 * @param array $search Optional Search parameters for the data being displayed.
 * @see ElementTable::applySearchFilters()
 * @return string HTML
 */
function select_element($props = array(), $value = null, $label=null, $search = array('record_types'=>array('All')))
{
    return _select_from_table('Element', $props, $value, $label, $search);
}

/**
 * @since 0.10
 * @param array $props Optional
 * @param mixed $value Optional
 * @param string|null $label Optional
 * @param array $search Optional
 * @uses _select_from_table()
 */
function select_entity($props = array(), $value = null, $label=null, $search = array())
{
    return _select_from_table('Entity', $props, $value, $label, $search);
}

/**
 * Use this to choose an item type from a <select>.
 *
 * @since 0.10
 * @uses ItemTypeTable::findAllForSelectForm()
 * @param array
 * @param string Selected value
 * @return string HTML
 */
function select_item_type($props=array(), $value=null, $label=null)
{
    return _select_from_table('ItemType', $props, $value, $label);
}

/**
 * Used primarily within the admin theme to build a <select> form input containing
 * the names and IDs of all elements that belong to the Item Type element set.
 *
 * Not meant to used by theme writers, possibly useful for plugin writers.
 *
 * @since 0.10
 * @param array
 * @param string|integer Optional value of the selected option.
 * @param string|null Optional Label for the form input.
 * @return string HTML
 */
function select_item_type_elements($props = array(), $value = null, $label = null)
{
    $searchParams = array(
        'element_set_name'=>ELEMENT_SET_ITEM_TYPE,
        'sort'=>'alpha');
    return _select_from_table('Element', $props, $value, $label, $searchParams);
}

/*
 * Used primarily in edit item type form to build a select form input containing
 * the names and IDs of all the data types.
 *
 * @since 1.2
 * @param array $props Optional
 * @param string $value Optional
 * @param string $label Optional
 * @return string HTML for selecting a data type.
 */
function select_data_types($props = array(), $value = null, $label = null)
{
    $searchParams = array();
    return _select_from_table('DataType', $props, $value, $label, $searchParams);
}

/**
 * @since 0.10
 * @internal Since select() is deprecated as of version 1.2, we will need to update
 * this function to use Zend Form.
 * @param string $tableClass Name of the table class to pull from.
 * @param array $props Optional XHTML attributes for the select input.
 * @param mixed $value Optional Value of the select input.
 * @param string|null $label Optional Label for the select input.
 * @param array $searchParams Optional Search parameters to filter the list of
 * parameters that are displayed.
 * @return string HTML for a <select> input.
 */
function _select_from_table($tableClass, $props = array(), $value = null, $label = null, $searchParams = array())
{
    $options = get_db()->getTable($tableClass)->findPairsForSelectForm($searchParams);
    $options = apply_filters(Inflector::underscore($tableClass) . '_select_options', $options);
    return select($props, $options, $value, $label);
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
