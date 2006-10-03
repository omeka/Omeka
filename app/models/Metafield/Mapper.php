<?php

class Metafield_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'metafields';
	protected $_unique_id	= 'metafield_id';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function doLoad( $array )
	{
		return new Metafield( $array );
	}
	
	public function targetClass()
	{
		return 'Metafield';
	}
	
	public function insert( Kea_Domain_Model $obj )
	{
		if( !$this->unique( 'metafield_name', $obj->metafield_name ) )
		{
			throw new Kea_DB_Mapper_Exception( 'Metafields must have unique names.' );
		}
		
		return parent::insert( $obj );
	}
	
	public function findByType( $id )
	{
		return $this->find()
					->joinLeft( 'types_metafields', 'types_metafields.metafield_id = metafields.metafield_id')
					->where( 'types_metafields.type_id = ?', $id )
					->execute();
	}
}

?>