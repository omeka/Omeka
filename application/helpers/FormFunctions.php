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
	
	function simple_search($props=array(),$uri) { ?>
		<form <?php echo _tag_attributes($props); ?> action="<?php echo $uri; ?>" method="get">
		<fieldset>
		    <input type="text" class="textinput" name="search" value="<?php echo htmlspecialchars($_REQUEST['search']); ?>"/>
		    <?php echo submit("Submit","submit_search"); ?>
		</fieldset>
		</form>
	 <?php }
	
	function items_search_form($props=array(), $uri, $toggleSearch=true) {
		?>
		<h2 id="search-header" class="close">Search Items</h2>
		
		<script type="text/javascript" charset="utf-8">
		//<![CDATA[
		
		//The functions that are used by the search form can be found in search.js
		
		<?php if($toggleSearch): //Only load the following javascripts if we are allowing the search to toggle ?>
			Event.observe(window,'load', Omeka.Search.toggleSearch);			
		<?php endif; ?>
			
			//Here is javascript that will duplicate the advanced-search form entries
			Event.observe(window,'load', Omeka.Search.activateSearchButtons );

		//]]>	
		</script>
			
		
		<form <?php echo _tag_attributes($props); ?> action="<?php echo $uri; ?>" method="get">
			
		<?php if ( $toggleSearch ): //Only display the search options if we enable toggling ?>
		  	<div id="search_choices">
				<span id="advanced_search_header">Show Advanced Options</span>
			</div>
		<?php endif; ?>
			
			<fieldset id="basic_search">
				<legend id="basic_search_header">Basic Search</legend>
				<input type="text" class="textinput" name="search" value="<?php echo h($_REQUEST['search']); ?>"/>
			</fieldset>
			<fieldset id="advanced_search">
				<legend id="advanced_search_header">Advanced Search</legend>
				
				<h3>Search by Specific fields</h3>
				
				<div id="advanced-search">
					
						<?php 
						//If the form has been submitted, retain the number of search fields used and rebuild the form
						if(!empty($_GET['advanced'])) {
							$search = $_GET['advanced'];
						}else {
							$search = array(array('field'=>'','type'=>'','value'=>''));
						}
						
						//Here is where we actually build the search form
						foreach ($search as $i => $rows): ?>
							<div class="search-entry">		
							<?php 
							//The POST looks like => 
							// advanced[0] =>
								//[field] = 'description'
								//[type] = 'contains'
								//[terms] = 'foobar'
							//etc
							echo select_element(
								array('name'=>"advanced[$i][element_id]"), 
								@$rows['element_id']); ?>
							
							<?php 
								echo select(
									array('name'=>"advanced[$i][type]"),
									array('contains'=>'contains', 'does not contain'=>'does not contain', 'is empty'=>'is empty', 'is not empty'=>'is not empty'),
									@$rows['type']
								); 
							?>
							
							<?php 
								echo text(
									array('name'=>"advanced[$i][terms]", 'size'=>20),
									@$rows['terms']); 
							?>
							
							<button type="button" class="add_search">+</button>
							<button type="button" class="remove_search">-</button>
							</div>		 				
						<?php endforeach; ?>	
						
					
				</div>
				
				<div id="search-by-range">
					<?php echo text(
						array('name'=>'range', 'class'=>'textinput'), 
						@$_GET['range'], 
						'Search by a range of ID#s (example: 1-4, 156, 79)'); ?>
				</div>
				
				<div id="search-selects">
			<?php 
			    echo label(array(), 'Search By Collection');
				echo select_collection(array('name'=>'collection'), $_REQUEST['collection']);
				echo label(array(), 'Search By Type');
				echo select_item_type(array('name'=>'type'), $_REQUEST['type']); 
			?>
			<?php if(has_permission('Users', 'browse')): ?>
			<?php 			
			    echo label(array(), 'Search By User');
				echo select_user(array('name'=>'user'), $_REQUEST['user']);
			?>
			<?php endif; ?>
			<label for="tags">Search by Tags</label>
				<input type="text" class="textinput" name="tags" value="<?php echo h($_REQUEST['tags']); ?>" />
			</div>
			<div id="search-checkboxes">
			<?php 
				if (has_permission('Items','showNotPublic')) {
				    echo checkbox(array('name'=>'public', 'id'=>'public'), $_REQUEST['public'], null, 'Only Public Items'); 
				}				
				
				echo checkbox(array('name'=>'featured', 'id'=>'featured'), $_REQUEST['featured'], null, 'Only Featured Items');
			?>
			</div>
			</fieldset>
			
			<?php fire_plugin_hook('append_to_search_form'); ?>
			<input type="submit" name="submit_search" id="submit_search" value="Search" />
			
		</form><?php
	}
?>