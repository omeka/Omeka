<?php

class Item_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'items';
	protected $_unique_id	= 'item_id';
	
	public function doLoad( $array )
	{
		return new Item( $array );
	}
	
	public function targetClass()
	{
		return 'Item';
	}
	
	public function singleUpdate( $field, $value, $obj_id )
	{
		return self::$_adapter->update( $this->_table_name, array( $field => $value), 'item_id = ' . $obj_id );
	}
	
	public function getTypeMetadata( Item $obj )
	{
		$select = self::$_adapter->select()
					->from('types_metafields', 'metafield_name, metatext_text, metafields.metafield_id, metafield_description, metatext.metatext_id' )
					->joinLeft( 'metafields', 'metafields.metafield_id = types_metafields.metafield_id' )
					->joinLeft( 'metatext', 'metatext.metafield_id = metafields.metafield_id' )
					->where( 'types_metafields.type_id = ?', $obj->type_id )
					->where( 'metatext.item_id = ?', $obj->item_id )
					->order( array( 'metatext_id' => 'ASC' ) );
			
		if( $result = $this->query( $select ) ) {
			if( $result->num_rows > 0) {
				while( $row = $result->fetch_assoc() ) {
					$obj->type_metadata[$row['metafield_id']] = array(	'metafield_id'			=> $row['metafield_id'],
														'metafield_name'		=> $row['metafield_name'],
														'metafield_description'	=> $row['metafield_description'],
														'metatext_id'			=> $row['metatext_id'],
														'metatext_text'			=> $row['metatext_text'] );
				}
				return $obj;
			} else {
				$result->free();
				$select = self::$_adapter->select()
							->from('types_metafields', 'metafield_name, metafield_description, metafields.metafield_id' )
							->joinLeft( 'metafields', 'metafields.metafield_id = types_metafields.metafield_id' )
							->where( 'types_metafields.type_id = ?', $obj->type_id )
							->order( array( 'metafield_id' => 'ASC' ) );;
				$result = $this->query( $select );
				while( $row = $result->fetch_assoc() ) {
					$obj->type_metadata[$row['metafield_id']] = array( 	'metafield_id'			=> $row['metafield_id'],
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
		$select->from( 'itemsTotal', '*' );
		return self::$_adapter->fetchOne( $select );
	}
	
	public function totalSliced( $type_id = null, $collection_id = null)
	{
		$select = self::$_adapter->select();
		$select->from( 'items', 'COUNT(*) as count' );
		if ( $type_id != null) $select->where( 'items.type_id = ?', $type_id );
		if ( $collection_id != null) $select->where( 'items.collection_id = ?', $collection_id );
		return self::$_adapter->fetchOne( $select );
	}
	
	/**
	 *	Foreign key constraints delete item, item-tag relationship, files, and location data
	 */
	public function delete( $id )
	{
		return self::$_adapter->delete( 'items', 'items.item_id = \'' . $id . '\'');
	}
}

?>