<?php
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
	 * @param boolean whether or not to return the label HTML
	 * @return mixed
	 **/
	function label($attributes, $text, $return = false) 
	{
		if(!is_array($attributes)) {
			$id = $attributes;
			$label = '<label'.(!$id ? '' : ' for="'.h($id).'"').'>'.$text.'</label>';;
		} else {
			$label = '<label '._tag_attributes($attributes).">".$text."</label>";
		}
		
		if($return) return $label;
		else echo $label;
	}
	
	function text( $attributes, $default=null, $label = null )
	{
		$input = '';
		if($label) 
		{
			label(@$attributes['id'],h($label));
		}
		
		if(is_array($attributes)) {
			if(!$default and !empty($attributes['value'])) 
			{
				$default = $attributes['value'];
				unset($attributes['value']);
			}			
		}
		
		$input .= '<input type="text"';
		if(!empty($attributes)) {
			$input .= ' '._tag_attributes($attributes);
		}
		$input .= ' value="'.h($default).'" ';
		
		$input .= '/>';
		$input .= "\n";
		echo $input;
	}
	
	function password( $attributes, $default=null, $label = null )
	{
		$input = '';
		if($label) 
		{
			label(@$attributes['id'],h($label));
		}
		
		if(!$default and !empty($attributes['value'])) 
		{
			$default = $attributes['value'];
			unset($attributes['value']);
		}
		
		$input .= '<input type="password"';
		if(!empty($attributes)) {
			$input .= ' '._tag_attributes($attributes);
		}
		$input .= ' value="'.h($default).'" ';
		
		$input .= '/>';
		$input .= "\n";
		echo $input;
	}

	/**
	 * 7/12/07 - $optionDesc can be complex like "%last_name%, %first_name%" instead of array('last_name','first_name')
	 *
	 **/
	function select( $attributes, $values = null, $default = null, $label=null, $optionValue = null, $optionDesc = null )
	{
		$select = '<select '._tag_attributes($attributes).'>';
		$select .= "\n\t" . '<option value="">Select Below&nbsp;</option>' . "\n"; 
		if( !$optionValue && !$optionDesc )
		{
			foreach( $values as $k => $v )
			{
				$select .= "\t" . '<option value="' . h( $k ) . '"';
				if( $default == $k ) $select .= ' selected="selected"';
				$select .= '>' . h( $v ) . '</option>' . "\n";
			}
		}
		else
		{
			foreach( $values as $obj_array )
			{
				$select .= "\t" . '<option value="' . h( $obj_array[$optionValue] ) . '"';
				if( $default == $obj_array[$optionValue] ) $select .= ' selected="selected"';
				$select .= '>';
				if( is_array( $optionDesc ) )
				{
					foreach( $optionDesc as $text )
					{
						$select .= h( $obj_array[$text] ) . ' ';
					}
					$select .= '</option>' . "\n";
				}
				elseif( is_string( $optionDesc ) )
				{
					//if we have % in the desc then its a complex description
					if(strpos($optionDesc, '%') !== false) {
						$desc = preg_match_all('/%(\w+)%/', $optionDesc, $matches);
	
						$search = $matches[0];
						$fields = $matches[1];
		
						foreach ($fields as $k => $field) {
							$optionDesc = str_replace($search[$k], $entity[$field], $optionDesc);
						}
						$select .= h( $optionDesc ) . '</option>' . "\n";
					}
					else {
						$select .= h( $obj_array[$optionDesc] ) . '</option>' . "\n";	
					}					
				}
			}
		}
		$select .= '</select>' . "\n";
		
		if($label) {
			if(is_string($attributes)) {
				label($attributes, h($label));
				echo "\n".$select;
			}
			//Label attribute must either have an associated id element or be wrapped around the select 
			//http://checker.atrc.utoronto.ca/servlet/ShowCheck?check=91
			elseif(array_key_exists('id',$attributes)) {
				label(@$attributes['id'],h($label));
				echo "\n".$select;
			}else {
				label(null,h($label) ."\n\t". $select);
			}
		}else {
			echo $select;
		}
	}
	

	function textarea($attributes, $default = null, $label = null )
	{
		if($label) label(@$attributes['id'],$label);
		$ta = '<textarea';
		if(!empty($attributes)) {
			$ta .= ' '._tag_attributes($attributes);
		}
		$ta .= '>' .  h($default)  . '</textarea>'."\n";
		echo $ta;
	}
	
	function radio( $attributes, array $values, $default = null, $label_class = 'radiolabel' )
	{
		foreach( $values as $k => $v )
		{
			
			$radio = '<label class="' . $label_class . '"><input type="radio"';
			if(!empty($attributes)) {
				$radio .= ' '._tag_attributes($attributes);
			}
			$radio .= ' value="' . h($k) . '"';
			if($default == $k )
			{
				$radio .= ' checked="checked" />' . h($v) . '</label>';
			}
			else
			{
				$radio .= ' />' . h($v) . '</label>';
			}
			echo $radio;
		}
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
	
	function checkbox($attributes, $checked = FALSE, $value=null, $label = null )
	{
		$checkbox = '<input type="checkbox"';
		if( $checked ) {
			$attributes['checked'] = 'checked';
		}
		if(!empty($attributes)) {
			$checkbox .= ' '._tag_attributes($attributes);
		}
		if($value) {
			$checkbox .= ' value="'.h($value).'"';
		}
		$checkbox .= ' />' . "\n";
		if($label) label(@$attributes['id'],$label);
		echo $checkbox;
	}
	
	function submit($value="Submit",$name="submit")
	{
		echo '<input type="submit" name="'.$name.'" id="'.$name.'" value="'.$value.'" />';
	}
	
	function simple_search($props=array(),$uri) { ?>
		<form <?php echo _tag_attributes($props); ?> action="<?php echo $uri; ?>" method="get">
		
		<input type="text" class="textinput" name="search" value="<?php echo h($_REQUEST['search']); ?>"/>
		<?php submit("Submit","submit_search"); ?>
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
				
				<?php 
					//We need to retrieve a list of all the core metadata fields and the extended type metafields
					$metafields = Metafield::names();
					$core_fields = Item::fields();
					$search_fields = array_merge($core_fields, $metafields);
					natsort($search_fields);
				?>
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
							select(
								array('name'=>"advanced[$i][field]"), 
								$search_fields, 
								@$rows['field']); ?>
							
							<?php 
								select(
									array('name'=>"advanced[$i][type]"),
									array('contains'=>'contains', 'does not contain'=>'does not contain', 'is empty'=>'is empty', 'is not empty'=>'is not empty'),
									@$rows['type']
								); 
							?>
							
							<?php 
								text(
									array('name'=>"advanced[$i][terms]", 'size'=>20),
									@$rows['terms']); 
							?>
							
							<button type="button" class="add_search">+</button>
							<button type="button" class="remove_search">-</button>
							</div>		 				
						<?php endforeach; ?>	
						
					
				</div>
				
				<div id="search-by-range">
					<?php text(
						array('name'=>'range', 'class'=>'textinput'), 
						@$_GET['range'], 
						'Search by a range of ID#s (example: 1-4, 156, 79)'); ?>
				</div>
				
				<div id="search-selects">
			<?php 
				select(array('name'=>'collection'), collections(), $_REQUEST['collection'], 'Search by Collection', 'id', 'name');
				select(array('name'=>'type'), types(), $_REQUEST['type'], 'Search by Type', 'id', 'name'); 
			?>
			<?php if(has_permission('Users', 'browse')): ?>
			<?php 			
				 select(array('name'=>'user'), users(), $_REQUEST['user'], 'Search By User', 'id', array('first_name', 'last_name'));
			?>
			<?php endif; ?>
			<label for="tags">Search by Tags</label>
				<input type="text" class="textinput" name="tags" value="<?php echo h($_REQUEST['tags']); ?>" />
			</div>
			<div id="search-checkboxes">
			<?php 
				if (has_permission('Items','showNotPublic')) {
				    checkbox(array('name'=>'public', 'id'=>'public'), $_REQUEST['public'], null, 'Only Public Items'); 
				}				
				
				checkbox(array('name'=>'featured', 'id'=>'featured'), $_REQUEST['featured'], null, 'Only Featured Items');
			?>
			</div>
			</fieldset>
			
			<?php fire_plugin_hook('append_to_search_form'); ?>
			<input type="submit" name="submit_search" id="submit_search" value="Search" />
			
		</form><?php
	}
	
	/**
	 * Create the form for an item's metatext entries, just so the theme writer doesn't have to know the mechanics
	 * Right now this is recursive to allow theme writer to use specific metafields
	 * 
	 * @todo Do we want this to take metafield names as arguments? Probably.
	 *
	 * @return void
	 **/
	function metatext_form($item, $input="textarea",$metafields=null) 
	{
		if(!empty($metafields)) {

			//Loop through the metafields
			if(is_array(current($metafields))) {
				foreach ($metafields as $key => $metafield) {
					metatext_form($item, $input, $metafield);
				}
			} else {
				$field = $metafields;
				$out = '';
				$input_id = strtolower(str_replace(' ', '_', $field['name']));

				$metafield_name = $field['name'];
				$metafield_value = $field['text'];
				$metafield_id = $field['metafield_id'];
				
				//Check for non-empty POST vars
				if($post_text = @$_POST['metafields'][$metafield_id]['text']) {
					$metafield_value = $post_text;
				}
				
				//Process a single metafield for this item
				switch ($input) {
					case 'text':
						$input = '<input type="text" class="textinput" name="metafields['.$metafield_id.'][text]" id="'.$input_id.'" value="'.h($metafield_value).'" />' . "\n\t";
						break;
					case 'textarea':
						$input = "\t" . '<textarea rows="15" cols="50" class="textinput" name="metafields['.$metafield_id.'][text]" id="'.$input_id.'">';
						$input .= h($metafield_value);
						$input .= '</textarea>' . "\n\t";
						break;
				}
				$out .= '<div class="field">';
				$out .= '<label for="'.$input_id.'">'.h($metafield_name);
				$out .=	'</label>'."\n\t";
				$out .= '<span class="tooltip" id="'.$input_id.'_tooltip">'. h($field['description']) .'</span>';
				$out .= $input;		
				$out .= '<input type="hidden" name="metafields['.$metafield_id.'][name]" value="' . h($metafield_name) . '" />';
				$out .= '</div>'."\n\n\t";

				echo $out;
			}
		}
	}
?>