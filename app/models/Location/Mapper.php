<?php
class Location_Mapper extends Kea_DB_Mapper
{	
	protected $_table_name	= 'location';
	protected $_unique_id	= 'location_id';
	
	public function doLoad( $array )
	{
		return new Location( $array );
	}

	public function targetClass()
	{
		return 'Location';
	}
	
	public function findByItem( $id )
	{
		return $this->find()
					->where( 'item_id = ?', $id )
					->execute();
	}
}
?>