<?php
	function text( array $params = array(), $desc = null )
	{
		$input = '<input type="text" ';
		foreach( $params as $key => $val )
		{
			$input .= $key . '="' . allhtmlentities( $val ) . '" ';
		}
		$input .= '/>';
		if( $desc )
		{
			$input .= allhtmlentities( $desc );
		}
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
?>