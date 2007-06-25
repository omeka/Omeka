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
			$attr[$key] = $key . '="' . allhtmlentities( $attribute ) . '"';
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
			$label = '<label'.(!$id ? '' : ' for="'.allhtmlentities($id).'"').'>'.$text.'</label>';;
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
			label(@$attributes['id'],allhtmlentities($label));
		}
		
		if(!$default and !empty($attributes['value'])) 
		{
			$default = $attributes['value'];
			unset($attributes['value']);
		}
		
		$input .= '<input type="text"';
		if(!empty($attributes)) {
			$input .= ' '._tag_attributes($attributes);
		}
		$input .= ' value="'.allhtmlentities($default).'" ';
		
		$input .= '/>';
		$input .= "\n";
		echo $input;
	}
	
	function password( $attributes, $default=null, $label = null )
	{
		$input = '';
		if($label) 
		{
			label(@$attributes['id'],allhtmlentities($label));
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
		$input .= ' value="'.allhtmlentities($default).'" ';
		
		$input .= '/>';
		$input .= "\n";
		echo $input;
	}

	function select( $attributes, $values = null, $default = null, $label=null, $optionValue = null, $optionDesc = null )
	{
		$select = '<select '._tag_attributes($attributes).'>';
		$select .= "\n\t" . '<option value="">Select Below&nbsp;</option>' . "\n"; 
		if( !$optionValue && !$optionDesc )
		{
			foreach( $values as $k => $v )
			{
				$select .= "\t" . '<option value="' . allhtmlentities( $k ) . '"';
				if( $default == $k ) $select .= ' selected="selected"';
				$select .= '>' . allhtmlentities( $v ) . '</option>' . "\n";
			}
		}
		else
		{
			foreach( $values as $obj_array )
			{
				$select .= "\t" . '<option value="' . allhtmlentities( $obj_array[$optionValue] ) . '"';
				if( $default == $obj_array[$optionValue] ) $select .= ' selected="selected"';
				$select .= '>';
				if( is_array( $optionDesc ) )
				{
					foreach( $optionDesc as $text )
					{
						$select .= allhtmlentities( $obj_array[$text] ) . ' ';
					}
					$select .= '</option>' . "\n";
				}
				elseif( is_string( $optionDesc ) )
				{
					$select .= allhtmlentities( $obj_array[$optionDesc] ) . '</option>' . "\n";	
				}
			}
		}
		$select .= '</select>' . "\n";
		
		if($label) {
			if(is_string($attributes)) {
				label($attributes, allhtmlentities($label));
				echo "\n".$select;
			}
			//Label attribute must either have an associated id element or be wrapped around the select 
			//http://checker.atrc.utoronto.ca/servlet/ShowCheck?check=91
			elseif(array_key_exists('id',$attributes)) {
				label(@$attributes['id'],allhtmlentities($label));
				echo "\n".$select;
			}else {
				label(null,allhtmlentities($label) ."\n\t". $select);
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
		$ta .= '>' .  $default  . '</textarea>'."\n";
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
			$radio .= ' value="' . $k . '"';
			if($default == $k )
			{
				$radio .= ' checked="checked" />' . $v . '</label>';
			}
			else
			{
				$radio .= ' />' . $v . '</label>';
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
		$input .= ' value="'.allhtmlentities($value).'"';
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
			$checkbox .= ' value="'.allhtmlentities($value).'"';
		}
		$checkbox .= ' />' . "\n";
		if($label) label(@$attributes['id'],$label);
		echo $checkbox;
	}
	
	function submit($value="Submit this Form --&gt;",$name="submit")
	{
		echo '<input type="submit" name="'.$name.'" value="'.$value.'" />';
	}
	
	function items_filter_form($props=array(), $uri) {
		?>
		<form <?php echo _tag_attributes($props); ?> action="<?php echo $uri; ?>" method="get">
			<fieldset>
				<?php 
					checkbox(array('name'=>'recent'), $_REQUEST['recent'], null, 'View Most Recent Items'); 
				?>
			</fieldset>
			
			<fieldset>
			<?php 
				select(array('name'=>'collection'), collections(), $_REQUEST['collection'], 'Filter by Collection', 'id', 'name');
				select(array('name'=>'type'), types(), $_REQUEST['type'], 'Filter by Type', 'id', 'name'); 
			?>
			<label>Filter by Tags<input type="text" name="tags" value="<?php echo $_REQUEST['tags']; ?>" /></label>
			</fieldset>
			
			<fieldset>
			<?php 
				checkbox(array('name'=>'public', 'id'=>'public'), $_REQUEST['public'], null, 'Only Public Items'); 
				checkbox(array('name'=>'featured', 'id'=>'featured'), $_REQUEST['featured'], null, 'Only Featured Items');
			?>
			</fieldset>
			
			<fieldset>
			<input type="text" name="search" value="<?php echo $_REQUEST['search']; ?>"/>
			<input type="submit" name="submit_search" value="Search" />
			</fieldset>
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
	function metatext_form($item, $type="textarea",$metafields=null,$usePlugins=false) 
	{
		if($metafields) {
			//Loop through the metafields
			if($metafields instanceof Doctrine_Collection) {
				foreach ($metafields as $key => $metafield) {
					metatext_form($item, $type, $metafield, $usePlugins );
				}
			} else {
				$metafield = $metafields;
				$out = '';
				$metafieldInputId = strtolower(str_replace(' ', '_', $metafield->name));
				//Process a single metafield for this item
				switch ($type) {
					case 'text':
						$input = '<input type="text" class="textinput" name="Metatext['.$metafield->id.'][text]" id="'.$metafieldInputId.'" value="'.$item->Metatext[$metafield->id]->text.'" />';
						break;
					case 'textarea':
						$input = '<textarea rows="15" cols="50" class="textinput" name="Metatext['.$metafield->id.'][text]" id="'.$metafieldInputId.'">';
						$input .= $item->Metatext[$metafield->id]->text;
						$input .= '</textarea>';
						break;
				}
				$out .= '<div class="field">';
				$out .= '<label for="'.$metafieldInputId.'">'.$metafield->name;
				$out .=	'</label>'."\n\t";
				$out .= $input;
				$out .= '</div>';
				
				$out .= '<input type="hidden" name="Metatext['.$metafield->id.'][metafield_id]" value="'.$metafield->id.'" />'."\n\n\t";
				echo $out;
			}
		} else {
			$metafields = $item->Metafields;

			metatext_form($item, $type, $metafields, $usePlugins );
		}
/*		
		$out = '';
		foreach($item->Type->Metafields as $key => $metafield) {
			
		}
		
		if($plugins) {
			
			foreach ($plugins as $key => $plugin) {
				foreach ($plugin->Metafields as $key => $metafield) {
					//Copied from above
					$out .= '<label>'.$metafield->name.'<textarea class="textinput" name="Metatext['
						.$metafield->id.'][text]">'.$item->Metatext[$metafield->id]->text
						.'</textarea><input type="hidden" name="Metatext['.$metafield->id
						.'][metafield_id]" value="'.$metafield->id.'"/>';
				}
			}
		}
		echo $out;
*/
	}
?>