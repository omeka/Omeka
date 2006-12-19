<?php

/**
 *
 *
 *
 */

require_once 'Kea/Domain/ObjectWatcher.php';
require_once 'Kea/DB/Adapter.php';
abstract class Kea_Domain_Model
{
	protected $validate = array();
	
	protected $validationErrors = array();

	protected static $_adapter;	
	protected $_table_name;
	protected $_unique_id;
	
	const VALID_WHEN_NULL	=	null;
	
	public function __construct( $array = null )
	{
		if( !self::$_adapter instanceof Kea_DB_Adapter ) {
			self::$_adapter = Kea_DB_Adapter::instance();
		}
		
		list($this->_table_name, $this->_unique_id, $adapter) = Kea_Domain_Model::staticSetup( $this->targetClass() );

		if( $array ) {
			foreach( $array as $k => $v ) {
				$this->$k = get_magic_quotes_gpc() ? stripslashes( $v ) : $v;
			}
		}
	}
	
	public function getId()
	{
		$id_name = strtolower( get_class( $this ) ) . '_id';
		return $this->$id_name;
	}
	
	public function setId( $val )
	{
		$id_name = strtolower( get_class( $this ) ) . '_id';
		$this->$id_name = $val;
		return $this;
	}
	
	public function collection()
	{
		return self::getCollection( get_class( $this ) );
	}
	
	public static function getCollection( $type )
	{
		return Kea_Domain_HelperFactory::getCollection( $type );
	}

	public function save()
	{
		if( $this->validates() ) {
			if( !$this->getId() ) {
				return $this->insert( $this );			
			} else {
				return $this->update( $this );
			}
		}
		return false;
	}
	/*
		array( 'object_title => array( '/regex/', 'error message') )
	
	*/
	public function validates()
	{
		if( count( $this->validate ) == 0 ) {
			return true;
		}
		
		foreach( $this->validate as $property => $validation_rule ) {
			if( is_array( $validation_rule ) )
			{
				$validator_msg = array_pop( $validation_rule );
				$valid = true;
				foreach( $validation_rule as $validator )
				{
					if( !preg_match( $validator, $this->$property ) )
					{
						$valid = false;
					}
					else
					{
						$valid = true;
					}
					if( !$valid )
					{
						$this->validationErrors[$property] = $validator_msg;
					}
				}
			}
		}
		return count( $this->validationErrors ) ? false : true;
	}
	
	public function getErrors()
	{
		return $this->validationErrors;
	}
	
	/**
	 * This boils an instance of Kea_Domain_Model down to its possible unique variables, searches the database for that combination and, if its unique, loads the entire entry.
	 * If the entry is not unique it returns null
	 *
	 * @return mixed Returns the found entry, otherwise null
	 * @author Kris Kelly
	 **/
	public function findExisting()
	{
		$vars = get_object_vars($this);
		$uniquevars = array_diff_key( $vars, get_class_vars('Kea_Domain_Model') );
		$id_name = strtolower( get_class( $this ) ) . '_id';
		unset($uniquevars[$id_name]);
		
		$mapper = $this->mapper();
		$select = $mapper->find();
		foreach( $uniquevars as $key => $value )
		{
			if( !empty($value) ) $select->where($key.' = ?', $value);
		}
		
		$res = $mapper->query( $select );

		if( $res->num_rows == 1 ) 
		{
			$row = $res->fetch_assoc();
			foreach( $row as $key => $value )
			{
				$this->$key = $value;
			}
			return $this;
		}
		else
		{
			return null;
		}
	}
	
	public function db()
	{
		return self::$_adapter;
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
	
	protected function doLoad( $array ) 
	{
		$class = $this->targetClass();
		return new $class($array);
	}
	
	protected function targetClass()
	{
		return get_class($this);
	}
	
	public function select( $fields = '*', $mapper = null, $use_plugins = true )
	{
		return self::$_adapter->select( $mapper, $use_plugins )->from( $this->_table_name, $fields );
	}
	
	public function find( $fields = '*', $use_plugins = true )
	{
		return $this->select( $fields, $this, $use_plugins );
	}
	
	public function toArray( Kea_Domain_Model $obj = null )
	{
		if(!$obj) $obj = $this;
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
	
	public function insert()
	{
		try{
			$result = self::$_adapter->insert( $this->_table_name, $this->toArray( $this ) );
			if(self::$_adapter->error()) {throw new Kea_DB_Mapper_Exception ( self::$_adapter->error() ); }
			$this->setId( self::$_adapter->insertId() );
			return $this;
		} catch (Kea_DB_Mapper_Exception $e)
		{
			die($e->__toString());
		}
	}
	
	public function update()
	{
		$where = $this->_unique_id . '= "' . $this->getId() . '"';
		$result = self::$_adapter->update( $this->_table_name, $this->toArray( $this ), $where );
		return $this;
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
	
	/**
	 * retrieve objects
	 *
	 * @param mysqli_result $result
	 * @param string $class
	 * @return array An array of model objects
	 * @author Kris Kelly
	 **/
	public function findObjects( $result, $class = null )
	{
		if( !$class ) $class = get_class($this);
//		$collection = Kea_Domain_HelperFactory::getCollection( $class );
//		$collection->__construct( $result, $this );
//		return $collection;
		$array = array();
		while( $row = $result->fetch_assoc() ) {
			array_push( $array, $this->doLoad($row) );
		}
		return $array;
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
	
	/**
	 * The whole idea of having a separate static constructor is retarded but since this class is abstract
	 * we can't instantiate a self-instance to handle this crap
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	private static function staticSetup( $class ) {
		//Gotta find the stupid ass tables because naming is not totally consistent
		$adapter = Kea_DB_Adapter::instance();
		$tables = $adapter->listTables();
		$matches = array_values(preg_grep('/'.strtolower($class).'/', $tables));
//		var_dump( $matches );exit;
		return array( $matches[0], 
					  strtolower($class).'_id',
					  $adapter );
		
	}
	
	protected static function doFindById( $id, $class ) 
	{
		$old = Kea_Domain_ObjectWatcher::exists( $class, $id );
		if ( $old )	return $old;
		
/*		$table = strtolower($class).'s';
		$unique_id = strtolower($class).'_id';
		$adapter = Kea_DB_Adapter::instance();
*/
		list($table, $unique_id, $adapter) = Kea_Domain_Model::staticSetup( $class );
		$sql = "SELECT * FROM $table WHERE $unique_id = :id";
		
		$res = $adapter->query( $sql, array('id' => $id) );
		$row = $res->fetch_assoc();
		$obj = new $class($row);
		Kea_Domain_ObjectWatcher::add( $obj );
		return $obj;
	}
	
	protected static function doTotal( $class )
	{
		//Duplicated from the doFindById method
/*		$table = strtolower($class).'s';
		$unique_id = strtolower($class).'_id';
		$adapter = Kea_DB_Adapter::instance();
*/
		list($table, $unique_id, $adapter) = Kea_Domain_Model::staticSetup( $class );
		$sql = "SELECT COUNT(*) FROM $table";
		return $adapter->fetchOne( $sql );
	}
	
	public function getStructure()
	{
	        return self::$_adapter->describeTable( $this->_table_name );
	}       
	
	/**
	 *	Foreign key constraints delete item, item-tag relationship, files, and location data
	 */
	public function delete( $id = null )
	{
		if(!$id) $id = $this->getId(); 
		return self::$_adapter->delete( $this->_table_name, $this->_table_name.'.'.$this->_unique_id.' = \'' . $id . '\'');
	}
}

?>