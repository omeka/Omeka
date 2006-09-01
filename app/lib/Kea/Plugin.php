<?php

abstract class Kea_Plugin
{
	protected $_adapter;
	
	protected $_validate = array();
	
	protected $_validation_errors = array();
	
	public function __construct()
	{
		$this->_adapter = Kea_DB_Adapter::instance();
	}
	
	public function validates()
	{
		if( count( $this->_validate ) == 0 ) {
			return true;
		}
		
		foreach( $this->validate as $property => $validation_rule ) {
			
			if( is_array( $validation_rule ) ) {
				$validator = $validation_rule[0];
				if( isset( $validation_rule[1] ) ) {
					$validator_msg = $validation_rule[1];
				}
			} elseif( is_string( $validation_rule ) ) {
				$validator = $validation_rule;
				$validator_msg = $property . ' is invalid.';
			}
			
			if( !preg_match( $validator, $this->$property ) ) {
				$this->_validation_errors[$property] = $validator_msg;
			}
		}
		
		return count( $this->_validation_errors ) ? false : true;
	}
}

?>