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
	
	function submit($value="Submit this Form",$name="submit")
	{
		echo '<input type="submit" name="'.$name.'" id="'.$name.'" value="'.$value.'" />';
	}
	
	function items_filter_form($props=array(), $uri) {
		?>
		<script type="text/javascript" charset="utf-8">
		//<![CDATA[

			//Here is javascript that will duplicate the advanced-search form entries
			Event.observe(window,'load', function() {
				var addButton = document.getElementsByClassName('add_search');
				
				//Make each button respond to clicks
				addButton.each(function(button) {
					Event.observe(button, 'click', addAdvancedSearch);
				});
				
				var removeButtons = document.getElementsByClassName('remove_search');
				
				removeButtons.each(function(button) {
					removeAdvancedSearch(button);
				});
			});
			
			function removeAdvancedSearch(button) {
				Event.observe(button, 'click', function() {
						button.up().destroy();
				});
			}
			
			function addAdvancedSearch() {
				//Copy the div that is already on the search form
				var oldDiv = $$('.search-entry').last();
				
				//Clone the div and append it to the form
				var div = oldDiv.cloneNode(true);
								
				oldDiv.up().appendChild(div);
				
				var inputs = $A(div.getElementsByTagName('input'));
				var selects = $A(div.getElementsByTagName('select'));
				
				//Find the index of the last advanced search formlet and inc it
				//I.e. if there are two entries on the form, they should be named advanced[0], advanced[1], etc
				var inputName = inputs[0].getAttribute('name');
				
				//Match the index, parse into integer, increment and convert to string again				
				var index = inputName.match(/advanced\[(\d+)\]/)[1];
				var newIndex = (parseInt(index) + 1).toString();
				
				//Reset the selects and inputs	
								
				inputs.each(function(i) {
					i.value = '';
					i.setAttribute('name', i.name.gsub(/\d+/, newIndex) );
				});
				selects.each(function(s) {
					s.selectedIndex = 0;
					s.setAttribute('name', s.name.gsub(/\d+/, newIndex) );
				});	
												
				//Make the button special again
				var add = div.getElementsByClassName('add_search').first();
				
				add.onclick = addAdvancedSearch;
				
				var remove = div.getElementsByClassName('remove_search').first();
				removeAdvancedSearch(remove);
			}
			
		//]]>	
		</script>
		
		
		<form <?php echo _tag_attributes($props); ?> action="<?php echo $uri; ?>" method="get">
			<fieldset>
				<legend>Search for Items</legend>
				<input type="text" class="textinput" name="search" value="<?php echo h($_REQUEST['search']); ?>"/>
			</fieldset>
			<fieldset>
				<legend>Advanced Search</legend>
				
				<?php 
					//We need to retrieve a list of all the core metadata fields and the extended type metafields
					$metafields = Metafield::names();
					$core_fields = Item::fields();
					$search_fields = array_merge($core_fields, $metafields);
					natsort($search_fields);
				?>
				
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
									array('name'=>"advanced[$i][terms]"),
									@$rows['terms']); 
							?>
							
							<button type="button" class="add_search">+</button>
							<button type="button" class="remove_search">-</button>
							</div>		 				
						<? endforeach; ?>	
						
					
				</div>
				
				<div id="search-selects">
			<?php 
				select(array('name'=>'collection'), collections(), $_REQUEST['collection'], 'Filter by Collection', 'id', 'name');
				select(array('name'=>'type'), types(), $_REQUEST['type'], 'Filter by Type', 'id', 'name'); 
			?>
			<?php if(has_permission('Users', 'browse')): ?>
			<?php 
				 select(array('name'=>'user'), users(), $_REQUEST['user'], 'Filter By User', 'id', array('first_name', 'last_name'));
			?>
			<?php endif; ?>
			<label for="tags">Filter by Tags</label>
				<input type="text" class="textinput" name="tags" value="<?php echo h($_REQUEST['tags']); ?>" />
			</div>
			<div id="search-checkboxes">
			<?php 
				checkbox(array('name'=>'recent'), $_REQUEST['recent'], null, 'Recent Items');
				checkbox(array('name'=>'public', 'id'=>'public'), $_REQUEST['public'], null, 'Only Public Items'); 
				checkbox(array('name'=>'featured', 'id'=>'featured'), $_REQUEST['featured'], null, 'Only Featured Items');
			?>
			</div>
			</fieldset>
			<input type="submit" name="submit_search" value="Search" />
			
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
				$out .= '<label for="'.$metafieldInputId.'">'.h($metafield_name);
				$out .=	'</label>'."\n\t";
				$out .= $input;
				$out .= '<input type="hidden" name="metafields['.$metafield_id.'][name]" value="' . h($metafield_name) . '" />';
				$out .= '</div>'."\n\n\t";

				echo $out;
			}
		}
	}
?>