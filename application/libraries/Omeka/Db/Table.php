<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Table classes are instantiated by Omeka_Db.  
 * 
 * Subclasses attached to models must follow the naming convention: model name + 
 * Table (e.g, ExhibitTable).
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Db_Table
{
    const SORT_PARAM = 'sort_field';
    const SORT_DIR_PARAM = 'sort_dir';

    /**
     * The name of the model that this table will retrieve objects for.
     *
     * @var string
     */
    protected $_target;
    
    /**
     * The name of the table (sans prefix).  If this is not given, it will
     * be inflected magically.
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The alias used for this table.  If not given, it will be inflected.
     * 
     * @see Omeka_Db_Table::getTableAlias()
     * @var string
     */
    protected $_alias;
    
    /**
     * Table prefix.
     * Generally used to differentiate Omeka installations sharing a DB. 
     *
     * @var string
     */
    protected $_tablePrefix;
    
    /**
     * @var Omeka_Db
     */
    protected $_db;
    
    /**
     * @internal Do not instantiate this by itself, only access instances via
     * Omeka_Db::getTable().
     * 
     * @param string $targetModel Class name of the table's model.
     * @param Omeka_Db $db Database object to use for queries.
     */
    public function __construct($targetModel, $db)
    {
        $this->_target = $targetModel;
        $this->_db = $db;
    }
    
    /**
     * Delegate to the DB adapter. Used primarily as a convenience method.
     * 
     * For example, now you can call fetchOne() from this table object.
     * 
     * @since 0.10
     * @param string $m Method name.
     * @param array $a Method arguments.
     * @return mixed
     */
    public function __call($m, $a)
    {
        if (!method_exists($this->_db, $m) && !method_exists($this->_db->getAdapter(), $m)) {
            throw new BadMethodCallException("Method named '$m' does not exist or is not callable.");
        }
        return call_user_func_array(array($this->_db, $m), $a);
    }
    
    /**
     * Retrieve the alias for this table.  
     * 
     * @internal HACK But it will do for now.
     * 
     * @return string
     */
    public function getTableAlias() {
        return !empty($this->_alias) ? $this->_alias : strtolower($this->_target[0]);
    }
    
    /**
     * Retrieve the Omeka_Db instance.
     * 
     * @return Omeka_Db
     */
    public function getDb()
    {
        return $this->_db;
    }
    
    /**
     * Determine whether or not a model has a given column.
     * 
     * @param string $field Field name.
     * @return bool
     */
    public function hasColumn($field)
    {
        $cols = $this->getColumns();
        
        return in_array($field, $cols);
    }

    /**
     * Retrieve a list of all the columns for a given model.
     * 
     * @internal This should be here and not in the model class because 
     * get_class_vars() returns private/protected properties when called from
     * within the class.  Will only return public properties when called in this
     * fashion.
     *
     * @return array
     */
    public function getColumns()
    {
        return array_keys(get_class_vars($this->_target));
    }
    
    /**
     * Retrieve the name of the table for the current table (used in SQL statements).
     * If the table name has not been set, then it will inflect the table name
     *
     * @uses Omeka_Db_Table::setTableName().
     * @return string
     */
    public function getTableName()
    {
        if (empty($this->_name)) {
           $this->setTableName();
        }
        
        //Return the table name with the prefix added
        return $this->getTablePrefix()  . $this->_name;
    }
    
    /**
     * Set the name of the database table accessed by this class.
     * 
     * If no name is provided, it will inflect the table name from the name of
     * the model defined in the constructor.  For example, Item --> items
     * 
     * @uses Inflector::tableize()
     * @param string $name (optional) Table name.
     * @return void
     */
    public function setTableName($name=null)
    {
        if ($name) {
            $this->_name = (string) $name;
        } else {
            $this->_name = Inflector::tableize($this->_target);
        }
    }

    /**
     * Retrieve the table prefix for this table instance.
     * 
     * @since 1.2
     * @return string
     */
    public function getTablePrefix()
    {
        if ($this->_tablePrefix === null) {
            $this->setTablePrefix();
        }
        return $this->_tablePrefix;
    }

    /**
     * Set the table prefix.  Defaults to the table prefix defined by the 
     * Omeka_Db instance.
     * 
     * In most cases, this should remain the default.  However, edge cases may 
     * require customization, e.g. creating wrappers for tables generated by other
     * applications.
     * 
     * @since 1.2
     * @param string|null $tablePrefix
     * @return void
     */
    public function setTablePrefix($tablePrefix = null)
    {
        if ($tablePrefix === null) {
            $this->_tablePrefix = $this->getDb()->prefix;
        } else {
            $this->_tablePrefix = $tablePrefix;
        }
    }

    /**
     * Retrieve a single record given an ID.
     *
     * @param integer $id
     * @return Omeka_Record|false
     */
    public function find($id)
    {        
        $select = $this->getSelectForFind($id);
        return $this->fetchObject($select, array());
    }
    
    /**
     * Get a set of objects corresponding to all the rows in the table
     * 
     * WARNING: This will be memory intensive and is thus not recommended for 
     * large data sets.
     *
     * @return array Array of {@link Omeka_Record}s.
     */
    public function findAll()
    {
        $select = $this->getSelect();
        return $this->fetchObjects($select);
    }
    
    /**
     * Retrieve an array of key=>value pairs that can be used as options in a 
     * <select> form input.  As it applies to the given table.
     * 
     * @uses Omeka_Db_Table::_getColumnPairs()
     * @param array $options (optional) Set of parameters for searching/filtering 
     * results.
     * @see Omeka_Db_Table::applySearchFilters()
     * @return array
     */
    public function findPairsForSelectForm(array $options = array())
    {
        $select = $this->getSelectForFindBy($options);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(array(), $this->_getColumnPairs());        
        $pairs = $this->getDb()->fetchPairs($select);
        return $pairs;
    }
    
    /**
     * Retrieve the array of columns that are used by findPairsForSelectForm().
     * 
     * This is a template method because these columns are different for every
     * table, but the underlying logic that retrieves the pairs from the 
     * database is the same in every instance.  This is an attempt to stay DRY.
     * 
     * @see Omeka_Db_Table::findPairsForSelectForm()
     * @return array
     */
    protected function _getColumnPairs()
    {
        throw new Exception('Column pairs must be defined in order to use ' .
            'Omeka_Db_Table::findPairsForSelectForm()!');
    }
    
    /**
     * Retrieve a set of model objects based on a given number of parameters
     * 
     * @uses Omeka_Db_Table::getSelectForFindBy()
     * @param array $params A set of parameters by which to filter the objects
     * that get returned from the DB.
     * @param integer $limit Number of objects to return per "page".
     * @param integer $page Page to retrieve.
     * @return array|null The set of objects that is returned
     */
    public function findBy($params=array(), $limit = null, $page = null)
    {
        $select = $this->getSelectForFindBy($params);

        if ($limit) {
            $this->applyPagination($select, $limit, $page);
        }
        return $this->fetchObjects($select);
    }
    
    /**
     * Retrieve a SELECT object for this table.
     * 
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $select = new Omeka_Db_Select($this->getDb()->getAdapter());
        $alias = $this->getTableAlias();
        $select->from(array($alias=>$this->getTableName()), "$alias.*");    
        return $select;    
    }
    
    /**
     * Retrieve a SELECT object that has had search filters applied to it.
     * 
     * @uses Omeka_Db_Table::getSelectForFindBy()
     * @param array $params optional Set of named search parameters.
     * @return Omeka_Db_Select
     */
    public function getSelectForFindBy($params=array())
    {
        $select = $this->getSelect();

        $sortParams = $this->_getSortParams($params);
        if ($sortParams) {
            list($sortField, $sortDir) = $sortParams;
            $this->applySorting($select, $sortField, $sortDir);
        }
        $this->applySearchFilters($select, $params);
        $this->_fireBrowseSqlHook($select, $params);
        return $select;
    }
    
    /**
     * Retrieve a SELECT object that is used for retrieving a single record from 
     * the database.
     * 
     * @param integer $recordId
     * @return Omeka_Db_Select
     */
    public function getSelectForFind($recordId)
    {
        //Cast to integer to prevent SQL injection
        $recordId = (int) $recordId;

        $select = $this->getSelect();
        $select->where( $this->getTableAlias().'.id = ?', $recordId);
        $select->limit(1);     
        
        return $select;   
    }
    
    /**
     * Apply a set of filters to a Select object based on the parameters 
     * given.
     * 
     * This template method must be implemented by subclasses in order to define
     * search behaviors.
     * 
     * @param Omeka_Db_Select $select
     * @param array $params
     * @return void
     */
    public function applySearchFilters($select, $params) {}

    /**
     * Applies default column-based sorting for a table.
     *
     * @param Omeka_Db_Select $select
     * @param string $sortField Field to sort on.
     * @param string $sortDir Direction to sort.
     */
    public function applySorting($select, $sortField, $sortDir)
    {
        if (empty($sortField) || empty($sortDir)) {
            return;
        }
        if (in_array($sortField, $this->getColumns())) {
            $alias = $this->getTableAlias();
            $select->order("$alias.$sortField $sortDir");
        }
    }
    
    /**
     * Apply pagination to a SELECT object via the LIMIT and OFFSET clauses.
     * 
     * @param Zend_Db_Select $select
     * @param integer $limit Number of results per "page".
     * @param integer|null $page Page to retrieve, first if omitted.
     * @return Zend_Db_Select
     */
    public function applyPagination($select, $limit, $page = null) 
    {
        if ($page) {
            $select->limitPage($page, $limit);   
        } else {
            $select->limit($limit);
        }
             
        return $select;
    }   
     
    /**
     * Retrieve an object or set of objects based on an SQL WHERE predicate.
     *
     * @param string $sqlWhereClause
     * @param array $params optional Set of parameters to bind to the WHERE
     * clause.  Used to prevent security flaws.
     * @param boolean $findOne optional Whether or not to retrieve a single
     * record or the whole set (retrieve all by default).
     * @return array|Omeka_Record|false
     */
    public function findBySql($sqlWhereClause, array $params=array(), $findOne=false)
    {
        $select = $this->getSelect();
        $select->where($sqlWhereClause);
        return $findOne ? $this->fetchObject($select, $params) : $this->fetchObjects($select, $params);
    }
    
    /**
     * Retrieve a count of all the rows in the table.
     *
     * @uses Omeka_Db_Table::getSelectForCount()
     * @param array $params optional Set of search filters upon which to base
     * the count.
     * @return integer
     */
    public function count($params=array())
    {
        $select = $this->getSelectForCount($params);
        return $this->getDb()->fetchOne($select);
    }
    
    /**
     * Retrieve a Select object that is used to retrieve a count of all the rows
     * in the table.
     * 
     * @param array $params optional Set of search filters.
     * @return Omeka_Db_Select
     */
    public function getSelectForCount($params=array())
    {
        $select = $params ? $this->getSelectForFindBy($params) : $this->getSelect();
        
        //Make sure the SELECT only pulls down the COUNT() column
        $select->reset(Zend_Db_Select::COLUMNS);        
        $alias = $this->getTableAlias();
        $select->from(array(), "COUNT(DISTINCT($alias.id))");
        
        //Reset the GROUP and ORDER BY clauses if necessary
        $select->reset(Zend_Db_Select::ORDER)->reset(Zend_Db_Select::GROUP);
        
        //Reset the LIMIT and OFFSET clauses if necessary
        $select->reset(Zend_Db_Select::LIMIT_COUNT)->reset(Zend_Db_Select::LIMIT_OFFSET);

        return $select;        
    }
    
    /**
     * Check whether or not a given row exists in the database.
     *
     * Currently used to verify that a row exists even though the current user 
     * may not have permissions to access it.
     *
     * @param int $id The ID of the row.
     * @return boolean
     */
    public function checkExists($id)
    {
        $alias = $this->getTableAlias();
        $select = $this->getSelectForCount()->where("$alias.id = ?", $id);
        $count = $this->getDb()->fetchOne($select);
        
        return ($count == 1);
    }
    
    /**
     * Retrieve a set of record objects based on an SQL SELECT statement.
     *
     * @param string $sql This could be either a string or any object that can
     * be cast to a string (commonly Omeka_Db_Select).
     * @param array $params Set of parameters to bind to the SQL statement.
     * @return array|null Set of Omeka_Record instances, or null if none can be
     * found.
     */
    public function fetchObjects($sql, $params=array())
    {        
        $res = $this->getDb()->query($sql, $params);
        $data = $res->fetchAll();
                            
        //Would use fetchAll() but it can be memory-intensive
        $objs = array();
        foreach ($data as $k => $row) {
            $objs[$k] = $this->recordFromData($row);
        }
        
        return $objs;
    }
    
    /**
     * Retrieve a single record from the database.
     * 
     * @see Omeka_Db_Table::fetchObjects()
     * @param string $sql
     * @param string $params Parameters to substitute into SQL query.
     * @return Omeka_Record
     */
    public function fetchObject($sql, array $params=array())
    {
        $row = $this->getDb()->fetchRow($sql, $params);
        return !empty($row) ? $this->recordFromData($row): null;
    }
    
    /**
     * Populate a record object with data retrieved from the database.
     * 
     * @todo FIXME: Should follow Zend coding standards for protected methods.
     * @param array $data A keyed array representing a row from the database.
     * @return Omeka_Record
     */
    protected function recordFromData(array $data)
    {
        $class = $this->_target;
        $obj = new $class($this->_db);
        $obj->setArray($data);
        return $obj;
    }

    /**
     * Get and parse sorting parameters to pass to applySorting.
     *
     * A sorting direction of 'ASC' will be used if no direction parameter
     * is passed.
     *
     * @param array $params
     * @return array|null Array of sort field, sort dir if params exist,
     * null otherwise.
     */
    private function _getSortParams($params)
    {
        if (array_key_exists(self::SORT_PARAM, $params)) {
            $sortField = trim($params[self::SORT_PARAM]);
            $dir = 'ASC';
            // Default to ascending sort with no dir param.
            if (array_key_exists(self::SORT_DIR_PARAM, $params)) {
                $sortDir = trim($params[self::SORT_DIR_PARAM]);
                if ($sortDir === 'a') {
                    $dir = 'ASC';
                } else if ($sortDir === 'd') {
                    $dir = 'DESC';
                }
            }
            return array($sortField, $dir);
        }
        return null;
    }

    /**
     * Fires a hook to allow plugins to alter the SQL SELECT query.
     *
     * @param Zend_Db_Select $select
     * @param array $params
     */
    private function _fireBrowseSqlHook($select, $params)
    {
        $modelName = Inflector::underscore($this->_target);
        fire_plugin_hook("{$modelName}_browse_sql", $select, $params);
    }
}
