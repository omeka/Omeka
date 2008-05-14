<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Database manager object for Omeka
 *
 * While mostly a wrapper for a Zend_Db_Adapter instance, this also provides shortcuts for
 * retrieving table objects and table names for use in SQL. 
 *
 * @uses Zend_Db_Adapter_Mysqli
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Db
{
	protected $_conn;
	
	/**
	 * All the table classes/names are stored here for easy access.  This could change in the future.
	 *
	 * @var array
	 **/
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

	/**
	 * The prefix that every table in the omeka database will use.  If null this is ignored
	 *
	 * @var string
	 **/
	public $prefix = null;
	
	/**
	 * @param Zend_Db_Adapter $conn A connection object courtesy of Zend Framework
	 * @param string $prefix The prefix for the database (if applicable)
	 * @return void
	 **/
	public function __construct($conn, $prefix=null)
	{
		$this->_conn = $conn;		
		$this->prefix = (string) $prefix;		
		
		$this->_log = $this->loggingEnabled();		
	}
	
	/**
	 * Delegate to the Zend_Db_Adapter instance.  Log queries if necessary
	 * 
	 * @todo Come up with a better solution for logging bad queries.  
	 *  Zend_Db_Profiler won't help with logging broken queries, so we need to 
	 *  keep this for the sake of logging those.
	 * @param string
	 * @param array
	 * @return mixed
	 **/
	public function __call($m, $a)
	{
	    //Log SQL for certain adapter calls
	    $logFor = array('fetchOne', 'fetchAll', 'prepare', 'query');
	    if(in_array($m, $logFor)) {
	        $this->log($a[0]);
	    }
	    
	    return call_user_func_array(array($this->_conn, $m), $a);
	}
		
	private function loggingEnabled()
	{
	    $config = Omeka_Context::getInstance()->getConfig('basic');
	    return (bool) $config->log->sql;
	}
	
	/**
	 * @deprecated
	 * 
	 * @param string
	 * @return void
	 **/
	public function getConnection()
	{
	    return $this->getAdapter();
	}
	
	public function getAdapter()
	{
	    return $this->_conn;
	}

	/**
	 * Retrieve the name of the table (including the prefix)
	 *
	 * @return string
	 **/
	public function getTableName($class) {
		$name = $this->_table_names[$class];
		
		return (string) $this->prefix . (string) $name;
	}

	public function hasTable($name) {
		return in_array($name, $this->_table_names) or array_key_exists($name, $this->_table_names);
	}
	
	/**
	 * A shortcut for checking to see whether the database tables have a prefix
	 *
	 * @return bool
	 **/
	public function hasPrefix() {
		return !empty($this->prefix);
	}
    
	public function getTable($class) {
		$tableClass = $class . 'Table';
        
		if(class_exists($tableClass)) {
			$table = new $tableClass($class, $this);
		}
		else {
			$table = new Omeka_Table($class, $this);
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

	/**
	 * Magic getter is a synonym for Omeka_Db::getTableName()
	 *
	 * @return string|null
	 **/
	public function __get($name)
	{
		return $this->getTableName($name);
	}
	
	/**
	 * Every query ends up looking like: 
	 *	INSERT INTO table (field, field2, field3, ...) VALUES (?, ?, ?, ...) 
	 *	ON DUPLICATE KEY UPDATE field = ?, field2 = ?, ...
	 *
	 * Note on portability: ON DUPLICATE KEY UPDATE is a MySQL extension.  
	 * The advantage to using this is that it doesn't care whether a row exists already.
	 * Basically it combines what would be insert() and update() methods in other ORMs into a single method
	 * 
	 * @return int The ID for the row that got inserted (or updated)
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

		// prepare and execute the statement
		$params = array_merge( $insert_params, $update_params);
		$this->exec($query, $params);

		return (int) $this->_conn->lastInsertId();
   }
	
	protected function log($sql)
	{
	    if($this->_log) {
	        Omeka_Context::getInstance()->getLogger()->debug((string) $sql);
	    }
	}
	
	/**
	 * @deprecated Since 4/30/08
	 * @param string
	 * @param array
	 * @return Zend_Db_Statement
	 **/
	public function exec($sql, $params=array())
	{	
        return $this->query($sql, $params);
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
}