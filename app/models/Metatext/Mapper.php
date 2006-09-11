<?php

class Metatext_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'metatext';
	protected $_unique_id	= 'metatext_id';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function doLoad( $array )
	{
		return new Metatext( $array );
	}
	
	public function targetClass()
	{
		return 'Metatext';
	}
	
	public function deleteByObject( $object_id )
	{
		return self::$_adapter->delete( 'metatext', 'metatext.object_id = \'' . $object_id . '\'');
	}
	
}

?>