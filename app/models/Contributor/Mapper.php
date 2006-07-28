<?php

class Contributor_Mapper extends Kea_DB_Mapper
{
	protected $_table_name	= 'contributors';
	protected $_unique_id	= 'contributor_id';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function doLoad( $array )
	{
		return new Contributor( $array );
	}
	
	public function targetClass()
	{
		return 'Contributor';
	}
	
	public function findById( $id )
	{
		return	$this->find()
					->where('contributor_id = ?', $id)
					->execute();
	}
	
	public function delete( $id )
	{
		self::$_adapter->delete( 'contributors', 'contributor_id = \'' . $id .'\'' );
		self::$_adapter->update( 'objects', array( 'contributor_id'	=> 'NULL' ), 'contributor_id = \'' . $id . '\'');
		return true;
	}
	
	public function alpha($type = 'object')
	{
		$select = $this->find()
		->order( array( 'contributors.contributor_last_name' => 'ASC' ) );
		
		switch( $type ) {
			case( 'object' ):
				return $this->execute();
			break;
				return $this->findArray($select);
			break;
		}
		
	}
	
}

?>