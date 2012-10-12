<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Database manager object for Omeka
 *
 * While mostly a wrapper for a Zend_Db_Adapter instance, this also provides 
 * shortcuts for retrieving table objects and table names for use in SQL.
 * 
 * @package Omeka\Db
 */
class Omeka_Db
{
    /**
     * The prefix that every table in the omeka database will use.
     *
     * @var string|null
     */
    public $prefix = null;
    
    /**
     * The database adapter.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;
    
    /**
     * All the tables that are currently managed by this database object.
     *
     * @var array
     */
    protected $_tables = array();
    
    /**
     * The logger to use for logging SQL queries. If not set, no logging will 
     * be done.
     *
     * @var Zend_Log|null
     */
    private $_logger;
    
    /**
     * @param Zend_Db_Adapter_Abstract $adapter A Zend Framework connection object.
     * @param string $prefix The prefix for the database tables, if applicable.
     */
    public function __construct($adapter, $prefix = null)
    {
        $this->_adapter = $adapter;
        $this->prefix = (string) $prefix;
    }
    
    /**
     * Delegate to the database adapter.
     * 
     * @param string $m Method name.
     * @param array $a Method arguments.
     * @return mixed
     */
    public function __call($m, $a)
    {
        if (!method_exists($this->_adapter, $m)) {
            throw new BadMethodCallException("Method named '$m' does not exist or is not callable.");
        }
        
        // Log SQL for certain adapter calls.
        $logFor = array('fetchOne', 'fetchAll', 'prepare', 'query', 'fetchRow', 
                        'fetchAssoc', 'fetchCol', 'fetchPairs');
        if (in_array($m, $logFor)) {
            $this->log($a[0]);
        }
        
        try {
            return call_user_func_array(array($this->_adapter, $m), $a);
            
        // Zend_Db_Statement_Mysqli does not consider a connection that returns 
        // a "MySQL server has gone away" error to be disconnected. Catch these 
        // errors, close the connection, and reconnect, then retry the query.
        } catch (Zend_Db_Statement_Mysqli_Exception $e) {
            if (2006 == $e->getCode()) {
                $this->_adapter->closeConnection();
                $this->_adapter->getConnection();
                return call_user_func_array(array($this->_adapter, $m), $a);
            }
            throw $e;
        }
    }
    
    /**
     * Magic getter is a synonym for Omeka_Db::getTableName().
     *
     * Example: $db->Item is equivalent to $db->getTableName('Item').
     *
     * @see Omeka_Db::getTableName()
     * @param string $name Property name; table model class name in this case.
     * @return string|null
     */
    public function __get($name)
    {
        return $this->getTableName($name);
    }
    
    /**
     * Set logger for SQL queries.
     *
     * @param Zend_Log $logger
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }
    
    /**
     * Retrieve the database adapter.
     *
     * @return Zend_Db_Adapter_Abstract
     */ 
    public function getAdapter()
    {
        return $this->_adapter;
    }
    
    /**
     * Retrieve the name of the table (including the prefix).
     *
     * @return string
     */
    public function getTableName($class) {
        return $this->getTable($class)->getTableName();
    }
        
    /**
     * Check whether the database tables have a prefix.
     *
     * @return boolean
     */
    public function hasPrefix() {
        return !empty($this->prefix);
    }
    
    /**
     * Retrieve a table object corresponding to the model class.
     * 
     * Table classes can be extended by inheriting off of Omeka_Db_Table and 
     * then calling your table Table_ModelName, e.g. Table_Item or 
     * Table_Collection. For backwards compatibility you may call your table 
     * ModelNameTable, i.e. ItemTable or CollectionTable. The latter naming 
     * pattern is deprecated.
     * 
     * This will cache every table object so that tables are not instantiated 
     * multiple times for complicated web requests.
     * 
     * @uses Omeka_Db::setTable()
     * @param string $class Model class name.
     * @return Omeka_Db_Table
     */
    public function getTable($class) {
        
        // Return the cached table object.
        if (array_key_exists($class, $this->_tables)) {
            return $this->_tables[$class];
        }
        
        // Set the expected table class names.
        $tableClass = "Table_$class";
        $tableClassDeprecated = "{$class}Table";
        
        if (class_exists($tableClass)) {
            $table = new $tableClass($class, $this);
        } else if (class_exists($tableClassDeprecated)) {
            $table = new $tableClassDeprecated($class, $this);
        } else {
            $table = new Omeka_Db_Table($class, $this);
        }
        
        // Cache the table object
        $this->setTable($class, $table);
        
        return $table;
    }
    
    /**
     * Cache a table object.
     *
     * Prevents the creation of unnecessary instances.
     *
     * @param string $alias
     * @param Omeka_Db_Table $table
     */
    public function setTable($alias, Omeka_Db_Table $table)
    {
        $this->_tables[$alias] = $table;
    }
    
    /**
     * Every query ends up looking like: 
     * INSERT INTO table (field, field2, field3, ...) VALUES (?, ?, ?, ...) 
     * ON DUPLICATE KEY UPDATE field = ?, field2 = ?, ...
     *
     * Note on portability: ON DUPLICATE KEY UPDATE is a MySQL extension.  
     * The advantage to using this is that it doesn't care whether a row exists already.
     * Basically it combines what would be insert() and update() methods in other 
     * ORMs into a single method
     * 
     * @param string $table Table model class name.
     * @param array $values Rows to insert (or update).
     * @return integer The ID for the row that got inserted (or updated).
     */
    public function insert($table, array $values = array())
    {
        if (empty($values)) {
            return false;
        }
        
        $table = $this->getTableName($table);
        
        // Column names are specified as array keys.
        $cols = array_keys($values);
        
        // Build the statement.
        $query = "INSERT INTO `$table` (`" . implode('`, `', $cols) . "`) VALUES (";
        $query .= implode(', ', array_fill(0, count($values), '?')) . ')';
        
        $insertParams = array_values($values);
        $updateQuery = array();
        $updateParams = $values;
        
        foreach ($cols as $col) {
            switch ($col) {
                case 'id':
                    $updateQuery[] = '`id` = LAST_INSERT_ID(`id`)';
                    // Since we're not actually using the 'id' param in the 
                    // UPDATE clause, remove it
                    unset($updateParams['id']);
                    break;
                default:
                    $updateQuery[] = "`$col` = ?";
                    break;
            }
        }
        
        // Build the update of duplicate key clause.
        $query .= ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updateQuery);
        
        // Prepare and execute the statement.
        $params = array_merge($insertParams, array_values($updateParams));
        $this->query($query, $params);
        
        return (int) $this->_adapter->lastInsertId();
    }
    
    /**
     * Log SQL query if logging is configured.
     * 
     * This logs the query before variable substitution from bind params.
     *
     * @param string|Zend_Db_Select $sql
     */
    protected function log($sql)
    {
        if ($this->_logger) {
            $this->_logger->debug((string) $sql);
        }
    }

    /**
     * Execute more than one SQL query at once.
     *
     * @param string $sql String containing SQL queries.
     * @param string $delimiter Character that delimits each SQL query.
     */
    public function queryBlock($sql, $delimiter = ';')
    {
        $queries = explode($delimiter, $sql);
        foreach ($queries as $query) {
            if (strlen(trim($query))) {
                $this->query($query);
            }
        }
    }

    /**
     * Read the contents of an SQL file and execute all the queries therein.
     * 
     * In addition to reading the file, this will make substitutions based on 
     * specific naming conventions. Currently makes the following substitutions:
     * %PREFIX% will be replaced by the table prefix.
     * 
     * @param string $filePath Path to the SQL file to load
     */
    public function loadSqlFile($filePath)
    {
        if (!is_readable($filePath)) {
            throw new InvalidArgumentException("Cannot read SQL file at '$filePath'.");
        }
        $loadSql = file_get_contents($filePath);
        $subbedSql = str_replace('%PREFIX%', $this->prefix, $loadSql);
        $this->queryBlock($subbedSql, ";\n");
    }
}
