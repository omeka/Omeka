<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_ThemeHelpers
 * @subpackage FormHelpers
 **/

/**
 * Generate attributes for XHTML tags.
 *
 * @since 0.9
 * @access private
 * @param array|string $attributes Attributes for the tag.  If this is a 
 * string, it will assign both 'name' and 'id' attributes that value for
 * the tag.
 * @param string
 * @return string
 **/
function _tag_attributes($attributes,$value=null) {

	if(is_string($attributes)) {
		$toProcess['name'] = $attributes;
		$toProcess['id'] = $attributes;
	}else {
		//don't allow 'value' to be set specifically as an attribute (why = consistency)
		unset($attributes['value']);
		$toProcess = $attributes;
	}
	
	$attr = array();
	foreach ($toProcess as $key => $attribute) {
		$attr[$key] = $key . '="' . html_escape( $attribute ) . '"';
	}
	return join(' ',$attr);
}

/**
 * Make a label for a form element.
 *
 * @since 0.9
 * @param mixed an array of attributes, or just the string id to be used in the 'for' attribute
 * @param string text of the form label
 * @return string
 **/
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
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param string|null $default
 * @param string|null $label
 * @return string HTML for the form element
 **/	
function text( $attributes, $default=null, $label = null )
{
    $html = '';
    
	if($label) {
	    // This is a hack to only apply the 'class' attribute to the input
	    // and not to the label 
	    $labelAttribs = $attributes;
	    unset($labelAttribs['class']);
	    $html .= __v()->formLabel($attributes['name'], $label, $labelAttribs);
	}
	
	$html .= __v()->formText($attributes['name'], $default, $attributes);
    return $html;
}

/**
 * Make a password form input.
 * 
 * @internal Facade for Zend_View_Helper_FormPassword.
 * @since 0.9
 * @param array $attributes XHTML attributes.
 * @param string|null $default Optional
 * @param string|null $label Optional
 * @return string
 **/	
function password( $attributes, $default=null, $label = null )
{
    $html = '';

	if($label) {
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
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param array|null $values Optional
 * @param string|null $default Optional
 * @param string|null $label Optional
 * @return string
 **/
function select( $attributes, $values = null, $default = null, $label=null)
{   
    $html = '';
    
    //First option is always the "Select Below" empty entry
    $values = (array) $values;
    $values = array('' => 'Select Below ') + $values;
        
    //Duplication
	if($label) {
	    $html .= __v()->formLabel($attributes['name'], $label, $attributes);
	}
	
    $html .= __v()->formSelect($attributes['name'], $default, $attributes, $values);
    
    return $html;
}
	
/**
 * Make a textarea form input.
 * 
 * @internal Facade for Zend_View_Helper_FormTextarea.
 * @since 0.9
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param string|null $default Optional
 * @param string|null $label Optional
 * @return string
 **/
function textarea($attributes, $default = null, $label = null )
{		
	$html = '';
	
	if($label) {
	    $html .= __v()->formLabel($attributes['name'], $label, $attributes);
	}
	
	$html .= __v()->formTextarea($attributes['name'], $default, $attributes);
	
	return $html;
}

/**
 * Make a form input that is a set of radio buttons.
 * 
 * @internal Facade for Zend_View_Helper_FormRadio.  
 * @since 0.9
 * @param array $attributes Set of XHTML attributes for the inputs.
 * @param array $values Key => value of the radio button, Value => label for the 
 * radio button.
 * @param string|null $default Optional
 * @param string $label_class Optional Defaults to 'radiolabel'.
 * @return string HTML
 **/	
function radio( $attributes, array $values, $default = null, $label_class = 'radiolabel' )
{
    $attributes['label_class'] = $label_class;
	return __v()->formRadio($attributes['name'], $default, $attributes, $values, null);
}

/**
 * Make a hidden form input.
 * 
 * @since 0.9
 * @param array $attributes Set of XHTML attributes for the form input.
 * @param string $value
 * @return string
 **/	
function hidden( $attributes, $value )
{
	$input = '<input type="hidden"';
	if(!empty($attributes)) {
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
 * @param array $attributes XHTML attributes.
 * @param boolean|null $checked Whether or not it should be checked by default.
 * @param string|null $value Optional Defaults to 1.
 * @param string|null $label Optional
 * @return string
 **/	
function checkbox($attributes, $checked = FALSE, $value=null, $label = null )
{
    if($checked !== null) {
        $attributes['checked'] = $checked;
    }

    $html = __v()->formCheckbox($attributes['name'], $value, $attributes);

	if($label) {
	    $html .= __v()->formLabel($attributes['name'], $label, $attributes);
	}
	
	return $html;
}

/**
 * Make a submit form input.
 * 
 * @since 0.9
 * @param array $attributes XHTML attributes.
 * @param string $value Optional Defaults to 'Submit'.
 * @return string
 **/	
function submit($attributes, $value="Submit")
{
    // This is a hack that makes this work.
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
 **/	
function simple_search($buttonText = "Search", $formProperties=array('id'=>'simple-search'), $uri = null) 
{ 
    // Always post the 'items/browse' page by default (though can be overridden).
    if (!$uri) {
        $uri = uri('items/browse');
    }
    
    $formProperties['action'] = $uri;
    $formProperties['method'] = 'get';
    $html  = '<form ' . _tag_attributes($formProperties) . '>' . "\n";
    $html .= '<fieldset>' . "\n\n";
    $html .= __v()->formText('search', html_escape($_REQUEST['search']), array('name'=>'textinput','class'=>'textinput'));
    $html .= __v()->formSubmit('submit_search', $buttonText);
    $html .= '</fieldset>' . "\n\n";
    $html .= '</form>';
    
    return $html;
}