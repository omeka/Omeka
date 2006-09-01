<?php

class Category_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'categories';
	protected $_unique_id	= 'category_id';
	
	public function doLoad( $array )
	{
		return new Category( $array );
	}
	
	public function targetClass()
	{
		return 'Category';
	}
	
	public function delete( $id )
	{
		// Delete category
		self::$_adapter->delete( $this->_table_name, 'category_id = "' . $id . '"' );
		
		// Delete metafield linkages
		self::$_adapter->delete( $this->_table_name.'_metafields', 'category_id = "' . $id . '"' );
		
		// Delete orphaned metafields
		self::$_adapter->delete( 'metafields', 'metafield_id NOT IN (SELECT metafield_id FROM categories_metafields)' );

		return true;	
	}

	protected function _total()
	{
		$select = self::$_adapter->select();
		$select->from( 'categories', 'COUNT(*) as total' );
		return self::$_adapter->fetchOne( $select );
	}

}

?>