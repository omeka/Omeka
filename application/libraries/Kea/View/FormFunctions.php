<?php
	function text( array $params = array(), $label = null )
	{
		$input = '';
		if($label) 
		{
			$input .= '<label'.( isset($params['id']) ? ' for="'.$params['id'].'"' : '' ).'>'.$label.'</label>';
		}
		
		$input .= '<input type="text" ';
		foreach( $params as $key => $val )
		{
			$input .= $key . '="' . allhtmlentities( $val ) . '" ';
		}
		$input .= '/>';
		$input .= "\n";
		echo $input;
	}
	

	function select( array $props = null, $val_array = null, $saved = null, $value = null, $desc = null )
	{
		$select = '<select ';
		foreach( $props as $k => $v )
		{
			$select .= $k . '="' . allhtmlentities( $v ) . '" ';
		}
		$select .= '>';
		$select .= "\t" . '<option value="">Select Below&nbsp;</option>' . "\n"; 
		if( !$value && !$desc )
		{
			foreach( $val_array as $k => $v )
			{
				$select .= "\t" . '<option value="' . allhtmlentities( $k ) . '"';
				if( $saved && $saved == $k ) $select .= ' selected="selected" ';
				$select .= '>' . allhtmlentities( $v ) . '</option>' . "\n";
			}
		}
		else
		{
			foreach( $val_array as $obj_array )
			{
				$select .= "\t" . '<option value="' . allhtmlentities( $obj_array[$value] ) . '"';
				if( $saved && $saved == $obj_array[$value] ) $select .= ' selected="selected" ';
				$select .= '>';
				if( is_array( $desc ) )
				{
					foreach( $desc as $text )
					{
						$select .= allhtmlentities( $obj_array[$text] ) . ' ';
					}
					$select .= '</option>' . "\n";
				}
				elseif( is_string( $desc ) )
				{
					$select .= allhtmlentities( $obj_array[$desc] ) . '</option>' . "\n";	
				}
			}
		}
		$select .= '</select>' . "\n";
		echo $select;
	}
	
	function textarea( array $params = array(), $text = null )
	{
		$ta = '<textarea ';
		foreach( $params as $key => $val ) {
			$ta .= ' ' . $key . '="' . $val . '"';
		}
		$ta .= '>' .  $text  . '</textarea>'."\n";
		echo $ta;
	}
	
	function radio( $name = null, array $values, $default = null, $saved = null, $label_class = 'radiolabel' )
	{
		foreach( $values as $k => $v )
		{
			
			$radio = '<label class="' . $label_class . '"><input type="radio" name="' . $name . '" value="' . $k . '"';
			if( $saved && $saved == $k )
			{
				$radio .= ' checked="checked" />' . $v . '</label>';
			}
			elseif( !$saved && $default == $k )
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
	
	function hidden( array $params = array() )
	{
		$input = '<input type="hidden" ';
		foreach( $params as $key => $val )
		{
			$input .= $key . '="' . allhtmlentities( $val ) . '" ';
		}
		$input .= ' />' . "\n";
		echo $input;
	}
	
	function checkbox( array $props, $checked = FALSE )
	{
		$checkbox = '<input type="checkbox" ';
		foreach( $props as $prop => $value )
		{
			$checkbox .= $prop . '="'. $value. '" ';
		}
		if( $checked ) $checkbox .= 'checked="checked" ';
		$checkbox .= ' />' . "\n";
		echo $checkbox;
	}
	
	/**
	 * Create the form for an item's metatext entries, just so the theme writer doesn't have to know the mechanics
	 * Right now this is recursive to allow theme writer to use specific metafields
	 * 
	 * @todo Do we want this to take metafield names as arguments? Probably.
	 *
	 * @return void
	 **/
	function metatext_form($item, $type="textarea", $metafields=null, $usePlugins=false ) 
	{
		if($metafields) {
			//Loop through the metafields
			if($metafields instanceof Doctrine_Collection_Batch) {
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
						$input = '<textarea class="textinput" name="Metatext['.$metafield->id.'][text] id="'.$metafieldInputId.'"">';
						$input .= $item->Metatext[$metafield->id]->text;
						$input .= '</textarea>';
						break;
				}
				
				$out .= '<label for="'.$metafieldInputId.'">'.$metafield->name;
				$out .= $input;
				$out .=	'</label>'."\n\t";
				$out .= '<input type="hidden" name="Metatext['.$metafield->id.'][metafield_id]" value="'.$metafield->id.'"/>'."\n\n\t";
				echo $out;
			}
		} else {
			metatext_form($item, $type, $item->Type->Metafields, $usePlugins );
			
			if($usePlugins) {
				$plugins = Doctrine_Manager::getInstance()->getTable('Plugin')->findActive();
				foreach ($plugins as $key => $plugin) {
					metatext_form($item, $type, $plugin->Metafields, $usePlugins);
				}
			}
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