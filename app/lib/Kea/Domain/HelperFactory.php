<?php

class Kea_Domain_HelperFactoryException extends Kea_Exception {}

class Kea_Domain_HelperFactory
{
	
	private function __construct() {}
	
	public static function getCollection( $type )
	{
		$collection = $type.'_Collection';
		if( class_exists( $collection ) ){
			return new $collection;	
		}else{
			throw new Kea_Domain_HelperFactoryException("Class $collection could not be found.");
		}
	}

	public static function getMapper( $type )
	{
		$mapper = $type.'_Mapper';
		if( class_exists( $mapper ) ){
			return new $mapper;	
		}else{
			throw new Kea_Domain_HelperFactoryException("Class $mapper could not be found.");
		}
	}
}

?>