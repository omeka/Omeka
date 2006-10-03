<?php
class TypesMetafields_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'types_metafields';
	protected $_unique_id	= null;
	
	public function insert( $cat_id, $mf_id )
	{	
		if( $this->joinExists( $cat_id, $mf_id ) ) {
			throw new Kea_DB_Mapper_Exception( 'The Type <=> Metafield join already exists.');
		}
		$array = array(
					'type_id'		=> $cat_id,
					'metafield_id'		=> $mf_id );

		$result = self::$_adapter->insert( $this->_table_name, $array );

		if( !$result ){
			throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		}
		return true;
	}
	
	public function delete( $cat_id, $mf_id )
	{
		if( !$this->joinExists( $cat_id, $mf_id ) )
		{
			throw new Kea_DB_Mapper_Exception( 'The Type #'.$cat_id.' <=> Metafield #'.$mf_id.' does not already exist.');
		}
		
		$result = self::$_adapter->delete( $this->_table_name, 'type_id = '.$cat_id.' AND metafield_id = '.$mf_id );
		
		if( !$result ){
			throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		}
		return true;
	}
	
	public function joinExists( $cat_id, $mf_id )
	{
		$stmt = self::$_adapter->select();
		$stmt->from( $this->_table_name )
			 ->where( 'type_id = ?', $cat_id )
			 ->where( 'metafield_id = ?', $mf_id );
		$res = self::$_adapter->query( $stmt );
		if( !$res ) throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		if( $res->num_rows > 0 ) {
			return true;
		}
		return false;
	}
	
	public function find_by_oc( $cat_id )
	{
		$select = self::$_adapter->select();
		$select->from( $this->_table_name )
				->where( 'type_id = ?', $cat_id );
		return self::$_adapter->query( $select );
	}
	
	public function doLoad( $array )
	{
		throw new Kea_DB_Mapper_Exception( 'Join table no Domain Model to load.');
	}
	
	public function targetClass()
	{
		throw new Kea_DB_Mapper_Exception( 'Join table, no target class.' );
	}
}
?>