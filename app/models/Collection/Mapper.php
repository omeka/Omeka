<?php

class Collection_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'collections';
	protected $_unique_id	= 'collection_id';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function doLoad( $array )
	{
		return new Collection( $array );
	}
	
	public function targetClass()
	{
		return 'Collection';
	}
	
	public function delete( $id )
	{
		self::$_adapter->delete( $this->_table_name, 'collection_id = "' . $id . '"' );
		return true;
	}
	
	public function addToCollection( $object_id, $collection_id )
	{
		$select = self::$_adapter->select();
		$select->from( 'objects_collections' )
				->where( 'object_id = ?', $object_id )
				->where( 'collection_id = ?', $collection_id );
		
		$result = self::$_adapter->query($select);
		
		if( $result->num_rows == 0 ) {
			return self::$_adapter->insert( 'objects_collections',
				array(	'object_id' => $object_id,
				 		'collection_id' => $collection_id ) );	
		} else {
			self::$_session->flash( 'This object is already in this collection.' );
			return null;
		}
	}

}

?>