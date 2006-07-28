<?php

class File_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'files';
	protected $_unique_id	= 'file_id';
	
	public function doLoad( $array )
	{
		return new File( $array );
	}
	
	public function targetClass()
	{
		return 'File';
	}
	
	public function total()
	{
		$select = self::$_adapter->select();
		$select->from( $this->_table_name, 'COUNT(*) as total');
		return self::$_adapter->fetchOne( $select );
	}
	
	public function delete( $file_id )
	{
		return self::$_adapter->delete( 'files', 'file_id=\'' . $file_id . '\'');
	}
}

?>