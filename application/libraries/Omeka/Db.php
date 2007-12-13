<?php 
/**
* Simplified wrapper for PDO
* holds table names as well
*/
class Omeka_Db
{
	protected $_conn;
	
	protected $_table_names = array(
		'Collection'=>'collections',
		'Entity'=>'entities',
		'EntitiesRelations'=>'entities_relations',
		'EntityRelationships'=>'entity_relationships',
		'Exhibit'=>'exhibits',
		'File'=>'files',
		'FilesImages'=>'files_images',
		'FilesVideos'=>'files_videos',
		'FileMetaLookup'=>'file_meta_lookup',
		'Item'=>'items',
		'ExhibitPageEntry'=>'items_section_pages',
		'Metafield'=>'metafields',
		'Metatext'=>'metatext',
		'Option'=>'options',
		'Person'=>'entities',
		'Institution'=>'entities',
		'Anonymous'=>'entities',
		'Plugin'=>'plugins',
		'ExhibitSection'=>'sections',
		'ExhibitPage'=>'section_pages',
		'Taggings'=>'taggings',
		'Tag'=>'tags',
		'Type'=>'types',
		'TypesMetafields'=>'types_metafields',
		'User'=>'users',
		'UsersActivations'=>'users_activations');

	public $prefix = null;
	
	public function __construct($conn, $prefix=null)
	{
		$this->_conn = $conn;
		
		if($conn instanceof PDO) {
			$this->_conn->getConnection()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
			
		$this->_conn->setFetchMode(Zend_Db::FETCH_ASSOC);
		
		$this->prefix = (string) $prefix;		
	}
	
	public function quote($value)
	{
		return $this->_conn->quote($value);
	}
	
	public function prepare($sql)
	{
		return $this->_conn->prepare($sql);
	}
	
	public function lastInsertId()
	{
		return $this->_conn->lastInsertId();
	}
	
	public function getTableName($class) {
		$name = $this->_table_names[$class];
		
		if($this->hasPrefix()) {
			return $this->prefix . $name;
		}
		else {
			return $name;
		}
	}

	public function hasTable($name) {
		return in_array($name, $this->_table_names) or array_key_exists($name, $this->_table_names);
	}

	public function hasPrefix() {
		return !empty($this->prefix);
	}

	public function getTable($class) {

		$tableClass = $class . 'Table';
		if(class_exists($tableClass)) {
			$table = new $tableClass($class);
		}
		else {
			$table = new Omeka_Table($class);
		}
		return $table;
	}
	
	/**
	 * Here's a quick hackaround for plugins adding their own tables to the database
	 *
	 * @return void
	 **/
	public function addTable($model_name, $table_name)
	{
		$this->_table_names[$model_name] = $table_name;
	}

	public function __get($name)
	{
		if($this->hasTable($name)) {
			return $this->getTableName($name);
		}
	}
	
	/**
	 * Every query ends up looking like: 
	 *	INSERT INTO table (field, field2, field3, ...) VALUES (?, ?, ?, ...) 
	 *	ON DUPLICATE KEY UPDATE field = ?, field2 = ?, ...
	 *
	 * @return void
	 **/
    public function insert($table, array $values = array()) {
		$table = $this->getTableName($table);
	
		if (empty($values)) {
		    return false;
		}
		// column names are specified as array keys
		$cols = array_keys($values);

		// build the statement
		$query = "INSERT INTO `$table`"
		       . ' (`' . implode('`, `', $cols) . '`) '
		       . 'VALUES (';

		
		$a = array();
		$a = array_fill(0, count($values), '?');
		$query .= implode(', ', $a) . ')';
		
		$insert_params = array_values($values);
/*
			$insert_query = array();
		foreach ($cols as $col) {
			$insert_query[] = $this->_conn->quote($values[$col]);
		}
		
		$query .= join(', ', $insert_query) . ')';
*/	
		
		$update_query = array();
		$update_params = $values;
		
		foreach ($cols as $col) {
			switch ($col) {
				case 'id':
					$update_query[] = "id=LAST_INSERT_ID(id)";
					
					//Since we're not actually using the 'id' param in the UPDATE clause, remove it
					unset($update_params['id']);
					break;
				default:
					$update_query[] = "`$col` = ?";
					break;
			}
		}
		$update_params = array_values($update_params);
		
		$query .= " ON DUPLICATE KEY UPDATE ". join(', ', $update_query);		

//Zend_Debug::dump( $query );exit;

		// prepare and execute the statement
		$params = array_merge( $insert_params, $update_params);
		$this->exec($query, $params);

		return (int) $this->_conn->lastInsertId();
   }
	
	/**
	 * Factory to determine the right Omeka database exception to throw in a given case
	 *
	 * @return Omeka_Db_Exception
	 * @return void
	 **/
	protected function throwOmekaDbException(array $errorInfo, PDOException $e, $sql)
	{
		if( ($errorInfo[0] == "23000") and ($errorInfo[1] == 1048)) {
			throw new Omeka_Db_NullColumnException($e, $sql);
		}
		throw new Omeka_Db_Exception($e, $sql);
	}
	
	//The only way to use PDO::exec() with prepared queries
	public function exec($sql, $params=array())
	{
		//Let's try a normal PDO::exec() if there are no parameters
		
		try {
			return $this->_conn->query($sql, $params);
		} 
		catch (Zend_Db_Statement_Exception $e) {
			Zend_Debug::dump( $e );exit;
		}
		catch (PDOException $e) {
			if($stmt) $errorInfo = $stmt->errorInfo();
			else $errorInfo = $this->_conn->errorInfo();
			
			$this->throwOmekaDbException($errorInfo, $e, $sql);
		}
	}
	
	//Use the PDO::query() with prepared queries
	public function query($sql, array $params=array(), $fetchMode=null)
	{
		
if($_GET['sql']) {
	if(!isset($this->queryCount)) $this->queryCount = 1;
	else $this->queryCount++;
	var_dump( (string) $sql );
	
	Zend_Debug::dump( $this->queryCount );
}				
		if(is_object($sql)) {
    		$sql = $sql->__toString();
		}
		
		try {
			$stmt = $this->_conn->query($sql, $params);
		}
		catch (Zend_Db_Statement_Exception $e) {
			throw new Omeka_Db_Exception($e, $sql);
		}
		 catch (PDOException $e) {
			throw new Omeka_Db_Exception($e, $sql);
		}
		
		if(!$fetchMode) {
			$stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
		}
		
		return $stmt;
	}
	
	//Ripped-off from Doctrine_Connection::fetchOne()
	public function fetchOne($sql, $params=array(), $colnum=0)
	{
		$res = $this->query($sql, $params);
		return $res->fetchColumn($colnum);
	}
	
	public function fetchCol($sql, $params=array())
	{
		$stmt = $this->query($sql, $params);
        $result = $stmt->fetchAll(Zend_Db::FETCH_COLUMN, 0);
        return $result;
	}
	
	/**
	 * Convenience function for when server setup disallows executing more than one SQL query at a time
	 *
	 * @return void
	 **/
	public function execBlock($sql)
	{
		$queries = explode(';', $sql);
		foreach ($queries as $query) {
			if(strlen(trim($query))) {
				$this->exec($query);
			}
		}
	}
		
	//Use this to fetch associative arrays using prepared queries
	public function fetchArray($sql, $params=array(), $key_column = null)
	{
		
		$res = $this->query($sql, $params);
		$res->setFetchMode(Zend_Db::FETCH_ASSOC);
		
		$return = array();
		
		foreach ($res as $key => $row) {
			if($key_column) {
				$return[$row[$key_column]] = $row;
			}else {
				$return[$key] = $row;
			}
		}
		
		return $return;
	}
	
	//Quick way to check whether or not a result set has rows in it
	public function hasRows(PDOStatement $result)
	{
		return $result->columnCount() > 0;
	}
}
 
?>
