<?php

abstract class Kea_DB_Mapper
{	
	protected static $_adapter;
	protected $_table_name;
	protected $_unique_id;

	//	Connect to the db
	public function __construct()
	{
		if( !self::$_adapter instanceof Kea_DB_Adapter ) {
			self::$_adapter = Kea_DB_Adapter::instance();
		}
	}
	
	//	Fetch a row of data from the results
	//	Return the value of abstract method doLoad
	public function load( mysqli_result $result, $row = null )
	{
		if( $row ) {
			$result->data_seek( $row );
		}
		$array = $result->fetch_assoc();
		return $this->loadArray( $array );
	}

	//	Takes an array and returns the result of abstract method doLoad
	//	Useful in the Iterator class
	public function loadArray( $array )
	{
		$old = $this->getFromMap( $array[$this->_unique_id] );
		if( $old ) {
			return $old;
		}
		$obj = $this->doLoad( $array );
		$this->addToMap( $obj );
		return $obj;
	}
	
	public function getFromMap( $id )
	{
		return Kea_Domain_ObjectWatcher::exists( $this->targetClass(), $id );
	}
	
	public function addToMap( Kea_Domain_Model $obj )
	{
		return Kea_Domain_ObjectWatcher::add( $obj );
	}
	
	protected abstract function doLoad( $array );
	protected abstract function targetClass();
	
	public function select( $fields = '*', $mapper = null, $use_plugins = true )
	{
		return self::$_adapter->select( $mapper, $use_plugins )->from( $this->_table_name, $fields );
	}
	
	public function find( $fields = '*', $use_plugins = true )
	{
		return $this->select( $fields, $this, $use_plugins );
	}
	
	protected function toArray( Kea_Domain_Model $obj )
	{
		$array = array();
		$ref = new ReflectionClass( $obj );
		$props = $ref->getProperties();
		foreach( $props as $prop )
		{
			if( $prop->isPublic() )
			{
				$name = $prop->name;
				if( isset( $obj->$name ) )
				{
					$array[$name] = $obj->$name;
				}	
			}
		}
		return $array;
	}
	
	public function insert( Kea_Domain_Model $obj )
	{
		$result = self::$_adapter->insert( $this->_table_name, $this->toArray( $obj ) );
		$obj->setId( self::$_adapter->insertId() );
		return $obj;
	}
	
	public function update( Kea_Domain_Model $obj )
	{
		$where = $this->_unique_id . '= "' . $obj->getId() . '"';
		$result = self::$_adapter->update( $this->_table_name, $this->toArray( $obj ), $where );
		return $obj;
	}
	
	public function total()
	{
		$select = self::$_adapter->select();
		$select->from( $this->_table_name, 'COUNT(*) as count' );
		return self::$_adapter->fetchOne( $select );
	}
	
	public function paginate( Kea_DB_Select $select, $page, $per_page = 9 )
	{
		$tmp_select = clone $select;
		$total_results = $this->query( $tmp_select );

		if( !$select->hasLimits() ) {
			$select->limitPage( $page, $per_page );	
		}

		$collection = $this->findObjects( $select );

		return array(	'page'			=> $page,
						'per_page'		=> $per_page,
						'total'			=> $total_results->num_rows,
		 				'objects'		=> $collection );
	}
	
	public function findObjects( Kea_DB_Select $select )
	{
		$result = $this->query( $select );
		$collection = Kea_Domain_HelperFactory::getCollection( $this->targetClass() );
		$collection->__construct( $result, $this );
		return $collection;
	}
	
	public function findArray( Kea_DB_Select $select )
	{
		return self::$_adapter->fetchAssoc( $select );
	}
	
	public function unique( $col, $val )
	{
		$select = self::$_adapter->select();
		$select->from( $this->_table_name, $col )
			   ->where( "$col = ?", $val );
		$result = $this->query( $select );
		if( $result->num_rows == 0 ) {
			return true;
		}
		return false;	
	}
		
	public function query( $stmt, array $bind = array() )
	{
		return self::$_adapter->query( $stmt, $bind );
	}
	
	public function allArray()
	{
		return $this->findArray( $this->select() );
	}
	
	public function allObjects()
	{
		return $this->findObjects( $this->select() );
	}
}

?>