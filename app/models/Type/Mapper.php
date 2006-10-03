<?php

class Type_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'types';
	protected $_unique_id	= 'type_id';
	
	public function doLoad( $array )
	{
		return new Type( $array );
	}
	
	public function targetClass()
	{
		return 'Type';
	}
	
	public function delete( $id )
	{
		// Delete type
		self::$_adapter->delete( $this->_table_name, 'type_id = "' . $id . '"' );
		
		// Delete metafield linkages
		self::$_adapter->delete( $this->_table_name.'_metafields', 'type_id = "' . $id . '"' );
		
		// Delete orphaned metafields
		self::$_adapter->delete( 'metafields', 'metafield_id NOT IN (SELECT metafield_id FROM types_metafields)' );

		return true;	
	}

	protected function _total()
	{
		$select = self::$_adapter->select();
		$select->from( 'types', 'COUNT(*) as total' );
		return self::$_adapter->fetchOne( $select );
	}

}

?>