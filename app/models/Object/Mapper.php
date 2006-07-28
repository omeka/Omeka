<?php

class Object_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'objects';
	protected $_unique_id	= 'object_id';
	
	public function doLoad( $array )
	{
		return new Object( $array );
	}
	
	public function targetClass()
	{
		return 'Object';
	}
	
	public function singleUpdate( $field, $value, $obj_id )
	{
		return self::$_adapter->update( $this->_table_name, array( $field => $value), 'object_id = ' . $obj_id );
	}
	
	public function getCategoryMetadata( Object $obj )
	{
		$select = self::$_adapter->select()
					->from('categories_metafields', 'metafield_name, metatext_text, metafields.metafield_id, metafield_description, metatext.metatext_id' )
					->joinLeft( 'metafields', 'metafields.metafield_id = categories_metafields.metafield_id' )
					->joinLeft( 'metatext', 'metatext.metafield_id = metafields.metafield_id' )
					->where( 'categories_metafields.category_id = ?', $obj->category_id )
					->where( 'metatext.object_id = ?', $obj->object_id )
					->order( array( 'metatext_id' => 'ASC' ) );
			
		if( $result = $this->query( $select ) ) {
			if( $result->num_rows > 0) {
				while( $row = $result->fetch_assoc() ) {
					$obj->category_metadata[] = array(	'metafield_id'			=> $row['metafield_id'],
														'metafield_name'		=> $row['metafield_name'],
														'metafield_description'	=> $row['metafield_description'],
														'metatext_id'			=> $row['metatext_id'],
														'metatext_text'			=> $row['metatext_text'] );
				}
				return $obj;
			} else {
				$result->free();
				$select = self::$_adapter->select()
							->from('categories_metafields', 'metafield_name, metafield_description, metafields.metafield_id' )
							->joinLeft( 'metafields', 'metafields.metafield_id = categories_metafields.metafield_id' )
							->where( 'categories_metafields.category_id = ?', $obj->category_id )
							->order( array( 'metafield_id' => 'ASC' ) );;
				$result = $this->query( $select );
				while( $row = $result->fetch_assoc() ) {
					$obj->category_metadata[] = array( 	'metafield_id'			=> $row['metafield_id'],
														'metafield_name'		=> $row['metafield_name'],
														'metafield_description'	=> $row['metafield_description'],
														'metatext_text'			=> null );
				}
			}
		} else {
			throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		}
	}
	
	public function total()
	{
		$select = self::$_adapter->select();
		$select->from( 'objectsTotal', '*' );
		return self::$_adapter->fetchOne( $select );
	}
	
	/**
	 *	Foreign key constraints delete object, object-tag relationship, files, and location data
	 */
	public function delete( $id )
	{
		return self::$_adapter->delete( 'objects', 'objects.object_id = \'' . $id . '\'');
	}
}

?>