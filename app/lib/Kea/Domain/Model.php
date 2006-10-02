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
	
	/**
	 * This boils an instance of Kea_Domain_Model down to its possible unique variables, searches the database for that combination and, if its unique, loads the entire entry.
	 * If the entry is not unique it returns null
	 *
	 * @return mixed Returns the found entry, otherwise null
	 * @author Kris Kelly
	 **/
	public function findExisting()
	{
		$vars = get_object_vars($this);
		$uniquevars = array_diff_key( $vars, get_class_vars('Kea_Domain_Model') );
		$id_name = strtolower( get_class( $this ) ) . '_id';
		unset($uniquevars[$id_name]);
		
		$mapper = $this->mapper();
		$select = $mapper->find();
		foreach( $uniquevars as $key => $value )
		{
			if( !empty($value) ) $select->where($key.' = ?', $value);
		}
		
		$res = $mapper->query( $select );

		if( $res->num_rows == 1 ) 
		{
			$row = $res->fetch_assoc();
			foreach( $row as $key => $value )
			{
				$this->$key = $value;
			}
			return $this;
		}
		else
		{
			return null;
		}
	}
}

?>