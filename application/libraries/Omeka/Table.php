<?php 
/**
* Table classes are instantiated by Omeka_Db.  
* A new instance is created for each call to a table method, so keep these lightweight
* Classes that override Omeka_Table must follow the naming convention: model name + Table (e.g, ExhibitTable)
* Classes that override Omeka_Table are not loaded automatically so must be req_once'd within the model itself
*/
class Omeka_Table
{
	//What kind of model should this table class retrieve from the DB
	protected $_target;
	
	public function __construct($targetModel)
	{
		$this->_target = $targetModel;
	}
	
	/**
	 * A wrapper for retrieving the database connection in table classes (may not be necessary)
	 *
	 * @return Omeka_Db
	 **/
	public function getConn()
	{
		return get_db();
	}
	
	/**
	 * Determine whether or not a model has a given column
	 *
	 * As of 12-11-07, only used in ItemTable class
	 * 
	 * @param string Field name
	 * @return bool
	 **/
	public function hasColumn($field)
	{
		$cols = $this->getColumns();
		
		return in_array($field, $cols);
	}

	/**
	 * Retrieve a list of all the columns for a given model
	 * 
	 * Note to self: This has to be here and not in the model itself because get_class_vars() returns private/protected
	 * when called inside its own class	
	 *
	 * @return array
	 **/
	public function getColumns()
	{
		return array_keys(get_class_vars($this->_target));
	}
	
	/**
	 * Retrieve the name of the table for the current table (used in SQL statements)
	 *
	 * @return string
	 **/
	public function getTableName()
	{
		$target = $this->_target;
		return $this->getConn()->$target;
	}
	
	/**
	 * Retrieve a single record given an ID
	 *
	 * @param int $id
	 * @return Omeka_Record | false
	 **/
	public function find($id)
	{		
		//Cast to integer to prevent SQL injection
		$id = (int) $id;

		$table = $this->getTableName();

		$sql = "SELECT t.* FROM $table t WHERE t.id = $id LIMIT 1";
//var_dump( $sql );exit;
		$records = $this->fetchObjects($sql);

		if (count($records) === 0) {
		    return false;
		}

		return current($records);
	}
	
	/**
	 * Get a set of objects corresponding to all the rows in the table
	 * 
	 * WARNING: This may be memory/time intensive and is not recommended for large data sets.
	 * So far this gets used for any model that does not paginate, i.e. all of them except Items.
	 *
	 * @return array
	 **/
	public function findAll()
	{
		$table = $this->getTableName();
		
		$sql = "SELECT t.* FROM $table t";
		
		return $this->fetchObjects($sql);
	}
	
	/**
	 * Return a set of objects based on a SQL WHERE predicate (see RoR / other frameworks)
	 *
	 * @return void
	 **/
	public function findBySql($sql, array $params=null, $findOne=false)
	{
		$table = $this->getTableName();
		
		$sql = "SELECT t.* FROM $table t WHERE $sql";
		
		return $this->fetchObjects($sql, $params, $findOne);
	}
	
	/**
	 * Retrieve a count of all the rows in the table
	 *
	 * @return int
	 **/
	public function count()
	{
		$table = $this->getTableName();
		
		$select = new Omeka_Select;
		$select->from("$table t ", "COUNT(DISTINCT(t.id))");
		
		return get_db()->fetchOne($select);
	}
	
	/**
	 * Check whether or not a given row exists in the database
	 *
	 * Right now this is used mainly to verify that a row exists even though the current user does not have permissions to access it
	 *
	 * @param int $id The ID of the row
	 * @return bool
	 **/
	public function checkExists($id)
	{
		$table = $this->getTableName();
		
		$select = new Omeka_Select;
		$select->from("$table t", "COUNT(DISTINCT(t.id))")
				->where("t.id = ?", $id);
				
		$count = get_db()->fetchOne($select);
		
		return ($count == 1);
	}
	
	/**
	 * Take a SQL SELECT statement and use the resulting data to populate record objects
	 *
	 * @param string $sql
	 * @param array $params To bind to prepared SQL statement
	 * @param bool $onlyOne If true, then return only the first object from the result set
	 * @return mixed - array of Omeka_Record | Omeka_Record | null | empty array
	 **/
	public function fetchObjects($sql, $params=array(), $onlyOne=false)
	{
		$db = $this->getConn();
		
		$res = $db->query($sql, $params);
		
		$data = $res->fetchAll();
		
		if(!count($data) or !$data) {
			return !$onlyOne ? array() : null;
		}
					
		if($onlyOne) return $this->recordFromData(current($data));
		
		//Would use fetchAll() but it can be memory-intensive
		$objs = array();
		foreach ($data as $k => $row) {
			$objs[$k] = $this->recordFromData($row);
		}
		
		return $objs;
	}
	
	/**
	 * @see Omeka_Table::fetchObjects()
	 *
	 * @return Omeka_Record
	 **/
	protected function recordFromData(array $data)
	{
		$class = $this->_target;
		$obj = new $class;
		$obj->setArray($data);
		return $obj;
	}
}

?>
