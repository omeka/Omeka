<?php

abstract class Kea_DB_Collection implements Iterator
{
	protected $mapper;
	protected $result;
	protected $total	= 0;
	protected $pointer	= 0;
	protected $objects	= array();
	protected $raw		= array();
	
	public function __construct( $result = null, $mapper = null )
	{
		if ( $result && $mapper ) {
			$this->init_db( $result, $mapper );
		}
	}
	
	// Make it so we can work with a collection of one as one
	public function __get( $prop )
	{
		if( $this->total == 1 ) {
			$obj = $this->getObjectAt( 0 );
			if( isset( $obj->{$prop} ) ) {
				return $obj->{$prop};
			}
			return false;
		}
	}
	
	public function __set( $prop, $val )
	{
		if( $this->total == 1 ) {
			$obj = $this->getObjectAt( 0 );
			$obj->{$prop} = $val;
			return $obj;
		}
	}
	
	// Make it so we can work with a collection of one as one
	public function __call( $method, $args )
	{
		if( $this->total == 1 ) {
			$obj = $this->getObjectAt( 0 );
			if( method_exists( $obj, $method ) ) {
				return call_user_func_array( array( $obj, $method ), $args );
			}
			return false;
		}
	}
	
	protected function init_db( mysqli_result $result, Kea_DB_Mapper $mapper )
	{
		$this->result = $result;
		$this->mapper = $mapper;
		$this->total += $result->num_rows;
		while( $row = $result->fetch_assoc() ) {
			$this->raw[] = $row;
		}
	}
	
	protected function doAdd( Kea_Domain_Model $object )
	{
		$this->objects[$this->total] = $object;
		$this->total++;
	}
	
	public function add( $obj )
	{
		$this->doAdd( $obj );
	}
	
	public function getObjectAt( $num )
	{	
		if( $num >= $this->total || $num < 0 ) {
			return null;
		}
		
		if( array_key_exists( $num, $this->objects ) ) {
			return $this->objects[$num];
		}
		
		if( $this->raw[$num] ){
			$this->objects[$num] = $this->mapper->loadArray( $this->raw[$num] );
			return $this->objects[$num];
		}
		
		return false;
	}
	
	public function rewind()
	{
		$this->pointer = 0;
	}
	
	public function current()
	{
		return $this->getObjectAt( $this->pointer );
	}
	
	public function key()
	{
		return $this->pointer;
	}
	
	public function next()
	{
		$row = $this->getObjectAt( $this->pointer );
		if( $row ) {
			$this->pointer++;
		}
		return $row;
	}
	
	public function valid()
	{
		return( !is_null( $this->current() ) );
	}
	
	public function total()
	{
		return $this->total;
	}
}

?>