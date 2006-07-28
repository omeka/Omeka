<?php

/**
 *
 *
 *
 */
abstract class Kea_Domain_Model
{
	protected $validate = array();
	
	protected $validationErrors = array();
	
	const VALID_WHEN_NULL	=	null;
	
	public function __construct( $array = null )
	{
		if( $array ) {
			foreach( $array as $k => $v ) {
				$this->$k = get_magic_quotes_gpc() ? stripslashes( $v ) : $v;
			}
		}
	}
	
	public function getId()
	{
		$id_name = strtolower( get_class( $this ) ) . '_id';
		return $this->$id_name;
	}
	
	public function setId( $val )
	{
		$id_name = strtolower( get_class( $this ) ) . '_id';
		$this->$id_name = $val;
		return $this;
	}
	
	public function collection()
	{
		return self::getCollection( get_class( $this ) );
	}
	
	public static function getCollection( $type )
	{
		return Kea_Domain_HelperFactory::getCollection( $type );
	}

	public function mapper()
	{
		return self::getMapper( get_class( $this ) );
	}

	public static function getMapper( $type )
	{
		return Kea_Domain_HelperFactory::getMapper( $type );
	}
	
	public function save()
	{
		if( $this->validates() ) {
			if( !$this->getId() ) {
				return $this->mapper()->insert( $this );			
			} else {
				return $this->mapper()->update( $this );
			}
		}
		return false;
	}
	/*
		array( 'object_title => array( '/regex/', 'error message') )
	
	*/
	public function validates()
	{
		if( count( $this->validate ) == 0 ) {
			return true;
		}
		
		foreach( $this->validate as $property => $validation_rule ) {
			if( is_array( $validation_rule ) )
			{
				$validator_msg = array_pop( $validation_rule );
				$valid = true;
				foreach( $validation_rule as $validator )
				{
					if( !preg_match( $validator, $this->$property ) )
					{
						$valid = false;
					}
					else
					{
						$valid = true;
					}
					if( !$valid )
					{
						$this->validationErrors[$property] = $validator_msg;
					}
				}
			}
		}
		return count( $this->validationErrors ) ? false : true;
	}
	
	public function getErrors()
	{
		return $this->validationErrors;
	}
}

?>