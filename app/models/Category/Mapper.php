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
	
	protected function _total()
	{
		$select = self::$_adapter->select();
		$select->from( 'categories', 'COUNT(*) as total' );
		return self::$_adapter->fetchOne( $select );
	}

}

?>