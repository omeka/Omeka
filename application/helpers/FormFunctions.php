<?php
/**
 * 
 * @todo Make sure all of these helper functions return HTML instead of 
 * echo'ing it.
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

    /**
     * Used to generate attributes for XHTML tags
     *
     * @access private
     * @param array|string $attributes Attributes for the tag.  If this is a 
     * string, it will assign both 'name' and 'id' attributes that value for
     * the tag.
     * @param string
     * @return void
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
			$attr[$key] = $key . '="' . h( $attribute ) . '"';
		}
		return join(' ',$attr);
	}

/**
 * Make a label for a form element
 *
 * @param mixed an array of attributes, or just the string id to be used in the 'for' attribute
 * @param string text of the form label
 * @return mixed
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
 * Facade for Zend_View_Helper_FormText.  This maintains the same function
 * signature as previous Omeka versions for backward compatibility.
 * 
 * @param array
 * @param string|null
 * @param string|null
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
	
function password( $attributes, $default=null, $label = null )
{
    $html = '';

	if($label) {
	    $html .= __v()->formLabel($attributes['name'], $label, $attributes);
	}
	
    $html .= __v()->formPassword($attributes['name'], $default, $attributes);
    
    return $html;
}

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
 * Facade for Zend_View_Helper_FormRadio.  
 * 
 * @param array
 * @param array Key => value of the radio button, Value => label for the 
 * radio button.
 * @param string|null
 * @return string HTML
 **/	
function radio( $attributes, array $values, $default = null, $label_class = 'radiolabel' )
{
    $attributes['label_class'] = $label_class;
	return __v()->formRadio($attributes['name'], $default, $attributes, $values, null);
}
	
	function hidden( $attributes, $value )
	{
		$input = '<input type="hidden"';
		if(!empty($attributes)) {
			$input .= ' '._tag_attributes($attributes);
		}
		$input .= ' value="'.h($value).'"';
		$input .= ' />' . "\n";
		echo $input;
	}

/**
 * Facade for Zend_View_Helper_FormCheckbox
 * 
 * @param array
 * @param boolean|null
 * @param string|null
 * @param string|null
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
	
function submit($value="Submit",$name="submit")
{
	return __v()->formSubmit($name, $value, array());
}
	
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
    $html .= __v()->formText('search', htmlspecialchars($_REQUEST['search']), array('name'=>'textinput'));
    $html .= __v()->formSubmit('submit_search', $buttonText);
    $html .= '</fieldset>' . "\n\n";
    $html .= '</form>';
    
    return $html;
}