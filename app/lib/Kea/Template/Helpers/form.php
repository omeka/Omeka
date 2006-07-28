<?php
class FormHelper
{
	
	public function text( array $params = array(), $desc = null )
	{
		$input = '<input type="text" ';
		foreach( $params as $key => $val )
		{
			$input .= $key . '="' . htmlentities( $val ) . '" ';
		}
		$input .= '>';
		if( $desc )
		{
			$input .= htmlentities( $desc );
		}
		$input .= ' />' . "\n";
		echo $input;
	}
	

	public function select( array $props = null, array $val_array = null, $saved = null, $value = null, $desc = null )
	{
		$select = '<select ';
		foreach( $props as $k => $v )
		{
			$select .= $k . '="' . htmlentities( $v ) . '" ';
		}
		$select .= '>';
		$select .= "\t" . '<option value="">Select Below&nbsp;</option>' . "\n";
		$select .= "\t" . '<option value="">Remove my Selection</option>' . "\n";
		if( !$value && !$desc )
		{
			foreach( $val_array as $k => $v )
			{
				$select .= "\t" . '<option value="' . htmlentities( $k ) . '"';
				if( $saved && $saved == $k ) $select .= ' selected ';
				$select .= '>' . htmlentities( $v ) . '</option>' . "\n";
			}
		}
		else
		{
			foreach( $val_array as $obj_array )
			{
				$select .= "\t" . '<option value="' . htmlentities( $obj_array[$value] ) . '"';
				if( $saved && $saved == $obj_array[$value] ) $select .= ' selected ';
				$select .= '>';
				if( is_array( $desc ) )
				{
					foreach( $desc as $text )
					{
						$select .= htmlentities( $obj_array[$text] ) . ' ';
					}
					$select .= '</option>' . "\n";
				}
				elseif( is_string( $desc ) )
				{
					$select .= htmlentities( $obj_array[$desc] ) . '</option>' . "\n";	
				}
			}
		}
		$select .= '</select>' . "\n";
		echo $select;
	}
	
	public function textarea( array $params = array(), $text = null )
	{
		$ta = '<textarea ';
		foreach( $params as $key => $val ) {
			$ta .= ' ' . $key . '="' . $val . '"';
		}
		$ta .= '>' . htmlentities( $text ) . '</textarea>'."\n";
		echo $ta;
	}
	
	public function radio( $name = null, array $values, $default = null, $saved = null, $label_class = 'radiolabel' )
	{
		foreach( $values as $k => $v )
		{
			
			$radio = '<label class="' . $label_class . '"><input type="radio" name="' . $name . '" value="' . $k . '"';
			if( $saved && $saved == $k )
			{
				$radio .= ' checked />' . $v . '</label><br/>';
			}
			elseif( !$saved && $default == $k )
			{
				$radio .= ' checked />' . $v . '</label><br/>';
			}
			else
			{
				$radio .= ' />' . $v . '</label><br/>';
			}
			echo $radio;
		}
	}
	
	public function hidden( array $params = array() )
	{
		$input = '<input type="hidden" ';
		foreach( $params as $key => $val )
		{
			$input .= $key . '="' . htmlentities( $val ) . '" ';
		}
		$input .= ' />' . "\n";
		echo $input;
	}
	
	public function displayError( $object, $property, array $error_array = array() )
	{
		if( isset( $error_array[$object][$property] ) ) {
			echo '<div class="form-error">Error: ' . $error_array[$object][$property] . '</div>' . "\n";
			return;
		}
		return false;
	}
	
}
?>